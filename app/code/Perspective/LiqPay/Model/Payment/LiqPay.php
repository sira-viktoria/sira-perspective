<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace  Perspective\LiqPay\Model\Payment;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Validator\Exception;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Model\Order;
use Perspective\LiqPay\Model\Config as PerspectiveConfig;
use Perspective\LiqPay\Sdk\LiqPay as SdkLiqPay;
/**
 * LiqPay Payment model.
 */
class LiqPay extends \Magento\Payment\Model\Method\AbstractMethod
{
    const METHOD_CODE = 'perspective_liqpay';

    protected $_code = self::METHOD_CODE;

    /**
     * @var PerspectiveConfig
     */
    protected PerspectiveConfig $perspectiveConfig;

    /**
     * @var SdkLiqPay
     */
    protected SdkLiqPay $sdkLiqPay;
    protected $_canCapture = true;
    protected $_canVoid = true;
    protected $_canUseForMultishipping = false;
    protected $_canUseInternal = false;
    protected $_isInitializeNeeded = true;
    protected $_isGateway = true;
    protected $_canAuthorize = false;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = false;
    protected $_canUseCheckout = true;
    protected $_minOrderTotal = 0;
    protected $supportedCurrencyCodes;

    /**
     * LiqPay Constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param SdkLiqPay $sdkLiqPay
     * @param PerspectiveConfig $perspectiveConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        SdkLiqPay $sdkLiqPay,
        PerspectiveConfig $perspectiveConfig,
        array                          $data = array()
    ) {
        $this->sdkLiqPay =         $sdkLiqPay;
        $this->perspectiveConfig = $perspectiveConfig;
        $this->supportedCurrencyCodes = $sdkLiqPay->_supportedCurrencies;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            null,
            null,
            $data
        );
    }

    /**
     * @param $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode): bool
    {
        if (!in_array($currencyCode, $this->supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    /**
     * @param InfoInterface $payment
     * @param $amount
     * @return $this|LiqPay
     * @throws Exception
     */
    public function capture(InfoInterface $payment, $amount): LiqPay|static
    {
        /** @var Order $order */
        $order = $payment->getOrder();
        try {
            $payment->setTransactionId('liqpay-' . $order->getId())->setIsTransactionClosed(0);
            return $this;
        } catch (\Exception $e) {
            $this->debugData(['exception' => $e->getMessage()]);
            throw new Exception(__('Payment capturing error.'));
        }
    }

    /**
     * @param CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(?CartInterface $quote = null): bool
    {
        if (!$this->perspectiveConfig->isEnabled()){
            return false;
        }

        return parent::isAvailable($quote);
    }
}
