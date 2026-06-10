<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Controller\Callback;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\ValidatorInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Sales\Model\Order;
use Perspective\LiqPay\Model\Config as PerspectiveConfig;
use Perspective\LiqPay\Model\Service\IsSecurityOrder;
use Perspective\LiqPay\Model\Service\LoggingData;
use Perspective\LiqPay\Model\Service\UpdateOrderStatus;
use Perspective\LiqPay\Sdk\LiqPay;
use Psr\Log\LoggerInterface;

/**
 * Index Controller.
 */
class Index extends Action implements HttpPostActionInterface, CsrfAwareActionInterface, ValidatorInterface
{
    /**
     * @var LiqPay
     */
    protected LiqPay $liqPay;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var PerspectiveConfig
     */
    protected PerspectiveConfig $perspectiveConfig;

    /**
     * @var IsSecurityOrder
     */
    protected IsSecurityOrder $isSecurityOrder;

    /**
     * @var UpdateOrderStatus
     */
    protected UpdateOrderStatus $updateOrderStatus;

    /**
     * @var LoggingData
     */
    protected LoggingData $loggingData;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param LiqPay $liqPay
     * @param LoggerInterface $logger
     * @param JsonFactory $resultJsonFactory
     * @param PerspectiveConfig $perspectiveConfig
     * @param IsSecurityOrder $isSecurityOrder
     * @param LoggingData $loggingData
     * @param UpdateOrderStatus $updateOrderStatus
     */
    public function __construct(
        Context $context,
        LiqPay $liqPay,
        LoggerInterface $logger,
        JsonFactory $resultJsonFactory,
        PerspectiveConfig $perspectiveConfig,
        IsSecurityOrder $isSecurityOrder,
        LoggingData $loggingData,
        UpdateOrderStatus $updateOrderStatus,
    ) {
        $this->liqPay = $liqPay;
        $this->logger = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->perspectiveConfig = $perspectiveConfig;
        $this->isSecurityOrder = $isSecurityOrder;
        $this->loggingData = $loggingData;
        $this->updateOrderStatus = $updateOrderStatus;
        parent::__construct($context);
    }

    public function execute()
    {
        $request = $this->getRequest();
        $data = $request->getPost('data');
        $signature = $request->getParam('signature');
        if (empty($data) || empty($signature)) {
            $this->logger->error('Missing required parameters.');
        }

        $this->loggingData->execute($data, $signature);

        if ($this->isSecurityOrder->execute($data, $this->perspectiveConfig->getPublicKey(), $signature)) {
            $decodedData = $this->liqPay->getDecodedData($data);
            $status = $decodedData['status'];
            $orderId = $decodedData['order_id'];

            switch ($status) {
                case LiqPay::STATUS_SUCCESS:
                case LiqPay::STATUS_WAIT_COMPENSATION:
                case LiqPay::STATUS_WAIT_RESERVE:
                    $this->updateOrderStatus->execute($orderId, Order::STATE_COMPLETE);
                    break;
                case LiqPay::STATUS_WAIT_SECURE:
                case LiqPay::STATUS_HOLD_WAIT:
                    $this->updateOrderStatus->execute($orderId, Order::STATE_HOLDED);
                    break;
                case LiqPay::STATUS_WAIT_ACCEPT:
                    $this->updateOrderStatus->execute($orderId, Order::STATE_PENDING_PAYMENT);
                    break;
                case LiqPay::STATUS_REVERSED:
                    $this->updateOrderStatus->execute($orderId, Order::STATE_CLOSED);
                    break;
                default:
                    $this->updateOrderStatus->execute($orderId, Order::STATE_CANCELED);
                    break;
            }
        } else {
            $this->logger->error('Invalid signature for LiqPay Callback');
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['status' => 'success']);
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Skip CSRF validation by returning true
     */
    public function validate(        RequestInterface $request,
                                     ActionInterface $action): void
    {
        return;
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }
}
