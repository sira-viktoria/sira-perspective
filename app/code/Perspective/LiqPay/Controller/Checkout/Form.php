<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Perspective\LiqPay\Model\Config as PerspectiveConfig;
use Perspective\LiqPay\Model\Service\IsLiqPayPaymentMethod;
use Perspective\LiqPay\Model\Service\IsSecurityOrder;

/**
 * Form Controller Class.
 */
class Form extends Action
{
    /**
     * @var CheckoutSession
     */
    protected CheckoutSession $checkoutSession;

    /**
     * @var LayoutFactory
     */
    protected LayoutFactory $layoutFactory;

    /**
     * @var PerspectiveConfig
     */
    protected PerspectiveConfig $perspectiveConfig;

    /**
     * @var IsLiqPayPaymentMethod
     */
    protected IsLiqPayPaymentMethod $isLiqPayPaymentMethod;

    /**
     * @var IsSecurityOrder
     */
    protected IsSecurityOrder $isSecurityOrder;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param LayoutFactory $layoutFactory
     * @param PerspectiveConfig $perspectiveConfig
     * @param IsLiqPayPaymentMethod $isLiqPayPaymentMethod
     * @param IsSecurityOrder $isSecurityOrder
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        LayoutFactory $layoutFactory,
        PerspectiveConfig $perspectiveConfig,
        IsLiqPayPaymentMethod $isLiqPayPaymentMethod,
        IsSecurityOrder $isSecurityOrder
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->layoutFactory = $layoutFactory;
        $this->perspectiveConfig = $perspectiveConfig;
        $this->isLiqPayPaymentMethod = $isLiqPayPaymentMethod;
        $this->isSecurityOrder = $isSecurityOrder;
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        try {
            if (!$this->perspectiveConfig->isEnabled()) {
                throw new \Exception('Payment is not allow.');
            }
            $order = $this->getCheckoutSession()->getLastRealOrder();
            if (!($order && $order->getId())) {
                throw new \Exception('Order not found');
            }
            if ($this->isLiqPayPaymentMethod->execute($order)) {
                $formBlock = $this->layoutFactory->create()->createBlock('Perspective\LiqPay\Block\SubmitForm');
                $formBlock->setOrder($order);
                $data = [
                    'status' => 'success',
                    'content' => $formBlock->toHtml(),
                ];
            } else {
                throw new \Exception('Order payment method is not a LiqPay payment method');
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong, please try again later'));
            $this->getCheckoutSession()->restoreQuote();
            $data = [
                'status' => 'error',
                'redirect' => $this->_url->getUrl('checkout/cart'),
            ];
        }
        /** @var Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData($data);
        return $result;
    }

    /**
     * Return checkout session object.
     *
     * @return CheckoutSession
     */
    protected function getCheckoutSession(): CheckoutSession
    {
        return $this->checkoutSession;
    }
}
