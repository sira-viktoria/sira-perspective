<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Model;

use Perspective\LiqPay\Api\LiqPayCallbackInterface;
use Magento\Sales\Model\Order;
use Perspective\LiqPay\Sdk\LiqPay;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;
use Perspective\LiqPay\Model\Config as PerspectiveConfig;
use Magento\Framework\App\RequestInterface;
use Perspective\LiqPay\Model\Service\IsLiqPayPaymentMethod;
use Perspective\LiqPay\Model\Service\IsSecurityOrder;
use Psr\Log\LoggerInterface;

/**
 * LiqPayCallback Class.
 */
class LiqPayCallback implements LiqPayCallbackInterface
{
    /**
     * @var Order
     */
    protected Order $order;

    /**
     * @var LiqPay
     */
    protected LiqPay $liqPay;

    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var InvoiceService
     */
    protected InvoiceService $invoiceService;

    /**
     * @var Transaction
     */
    protected Transaction $transaction;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var IsLiqPayPaymentMethod
     */
    protected IsLiqPayPaymentMethod $isLiqPayPaymentMethod;

    /**
     * @var IsSecurityOrder
     */
    protected IsSecurityOrder $isSecurityOrder;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    public function __construct(
        Order $order,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        Transaction $transaction,
        PerspectiveConfig $helper,
        LiqPay $liqPay,
        RequestInterface $request,
        IsLiqPayPaymentMethod $isLiqPayPaymentMethod,
        IsSecurityOrder $isSecurityOrder,
        LoggerInterface $logger
    ) {
        $this->order = $order;
        $this->liqPay = $liqPay;
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->helper = $helper;
        $this->request = $request;
        $this->isLiqPayPaymentMethod = $isLiqPayPaymentMethod;
        $this->isSecurityOrder = $isSecurityOrder;
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function callback(): mixed
    {
        $post = $this->request->getParams();

//        $post1 = [
//            'payment_id' => 2870079233, 'action' => 'pay', 'status' => 'sandbox', 'version' => 3,
//            'type' => 'buy', 'paytype' => 'card',
//            'public_key' => 'sandbox_i73981245824',
//            'acq_id' => '414963',
//            'order_id' => '000000053',
//            'liqpay_order_id' => 'EKGGTUBF1781078176',
//            'description' => 'Sira',
//            'sender_first_name' => 'Viktoriia',
//            'sender_last_name' => 'Sira',
//            'sender_card_mask2' => '424242',
//            'sender_card_bank' => 'Test',
//            'sender_card_type' => 'visa',
//            'sender_card_country' => '804',
//            'ip' => '176.116.93.1',
//            'sender_commission'=>'0',
//            'receiver_commission'=>'0',
//            'agent_commission'=>'0',
//            'amount_debit'=>'1764.71',
//            'amount_credit'=>'1764.71',
//            'commission_debit'=>'0',
//            'commission_credit'=>'0',
//            'currency_debit'=>'UAH',
//            'currency_credit'=>'UAH',
//            'bonus'=>'0',
//            'is_3ds'=>'0',
//            'language'=>'uk',
//            'create_date'=>'1781078176346',
//            'end_date'=>'1781078176412',
//            'transaction_id'=>'2870079233',
//        ];
//        $post =
//            [
//                'data' => $post1,
//                'signature' => 'oR4dePT1dX6WC571zpPw8GaRHcQ'
//
//        ];
        if (!(isset($post['data']) && isset($post['signature']))) {
            $this->logger->critical(__('In the response from LiqPay server there are no POST parameters "data" and "signature"'));
            return null;
        }

        $data = $post['data'];
        $receivedSignature = $post['signature'];

        $decodedData = $this->liqPay->getDecodedData($data);

        $orderId = $decodedData['order_id'] ?? null;
        $receivedPublicKey = $decodedData['public_key'] ?? null;
        $status = $decodedData['status'] ?? null;
        $amount = $decodedData['amount'] ?? null;
        $currency = $decodedData['currency'] ?? null;
        $transactionId = $decodedData['transaction_id'] ?? null;
        $senderPhone = $decodedData['sender_phone'] ?? null;


        try {
            $order = $this->getRealOrder($orderId);
            if (!($order && $order->getId() && $this->isLiqPayPaymentMethod->execute($order))) {
                return null;
            }
            if (!$this->isSecurityOrder->execute($data, $receivedPublicKey, $receivedSignature)) {
                $order->addStatusHistoryComment(__('LiqPay security check failed!'));
                $this->orderRepository->save($order);
                return null;
            }
            $state = null;
            switch ($status) {
                case LiqPay::STATUS_SANDBOX:
                case LiqPay::STATUS_WAIT_COMPENSATION:
                // case LiqPay::STATUS_SUBSCRIBED:
                case LiqPay::STATUS_SUCCESS:
                    if ($order->canInvoice()) {
                        $invoice = $this->invoiceService->prepareInvoice($order);
                        $invoice->register()->pay();
                        $transactionSave = $this->transaction->addObject(
                            $invoice
                        )->addObject(
                            $invoice->getOrder()
                        );
                        $transactionSave->save();
                        if ($status == LiqPay::STATUS_SANDBOX) {
                            $historyMessage[] = __('Invoice #%1 created (sandbox).', $invoice->getIncrementId());
                        } else {
                            $historyMessage[] = __('Invoice #%1 created.', $invoice->getIncrementId());
                        }
                        $state = Order::STATE_PROCESSING;
                    } else {
                        $historyMessage[] = __('Error during creation of invoice.');
                    }
                    if ($senderPhone) {
                        $historyMessage[] = __('Sender phone: %1.', $senderPhone);
                    }
                    if ($amount) {
                        $historyMessage[] = __('Amount: %1.', $amount);
                    }
                    if ($currency) {
                        $historyMessage[] = __('Currency: %1.', $currency);
                    }
                    break;
                case LiqPay::STATUS_FAILURE:
                    $state = Order::STATE_CANCELED;
                    $historyMessage[] = __('Liqpay error.');
                    break;
                case LiqPay::STATUS_ERROR:
                    $state = Order::STATE_CANCELED;
                    $historyMessage[] = __('Liqpay error.');
                    break;
                case LiqPay::STATUS_WAIT_SECURE:
                    $state = Order::STATE_PROCESSING;
                    $historyMessage[] = __('Waiting for verification from the Liqpay side.');
                    break;
                case LiqPay::STATUS_WAIT_ACCEPT:
                    $state = Order::STATE_PROCESSING;
                    $historyMessage[] = __('Waiting for accepting from the buyer side.');
                    break;
                case LiqPay::STATUS_WAIT_CARD:
                    $state = Order::STATE_PROCESSING;
                    $historyMessage[] = __('Waiting for setting refund card number into your Liqpay shop.');
                    break;
                default:
                    $historyMessage[] = __('Unexpected status from LiqPay server: %1', $status);
                    break;
            }
            if ($transactionId) {
                $historyMessage[] = __('LiqPay transaction id %1.', $transactionId);
            }
            if (count($historyMessage)) {
                $order->addStatusHistoryComment(implode(' ', $historyMessage))
                    ->setIsCustomerNotified(true);
            }
            if ($state) {
                $order->setState($state);
                $order->setStatus($state);
                $this->orderRepository->save($order);
            }
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        return null;
    }

    /**
     * @param $orderId
     * @return Order
     */
    protected function getRealOrder($orderId): Order
    {
        return $this->order->loadByIncrementId($orderId);
    }
}
