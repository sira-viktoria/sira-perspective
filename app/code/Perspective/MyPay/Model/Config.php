<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\MyPay\Model;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Config Model.
 */
class Config
{
    /**
     * Paths to Configurations.
     */
    private const XML_PATH_PERSPECTIVE_GENERL_ENABLED = 'sales/perspective/general/enabled';
    private const XML_PATH_PERSPECTIVE_TRADE_CUSTOMER_GROUP = 'sales/perspective/trade/customer_group';
    private const XML_PATH_PERSPECTIVE_TRADE_QTY_OF_PRODUCTS = 'sales/perspective/trade/quantity_of_products';
    private const XML_PATH_PERSPECTIVE_TRADE_ALLOWED_SHIPPING = 'sales/perspective/trade/allowed_shipping';
    private const XML_PATH_PERSPECTIVE_TRADE_ALLOWED_PAYMENT = 'sales/perspective/trade/allowed_payment';
    private const XML_PATH_PERSPECTIVE_LARGE_TRADE_CUSTOMER_GROUP = 'sales/perspective/large_trade/customer_group';
    private const XML_PATH_PERSPECTIVE_LARGE_TRADE_AMOUNT_FOR_FREE_SHIPPING = 'sales/perspective/large_trade/amount_for_free_shipping';
    private const XML_PATH_PERSPECTIVE_LARGE_TRADE_ALLOWED_PAYMENT = 'sales/perspective/large_trade/allowed_payment';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface  $serializer
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PERSPECTIVE_GENERL_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    public function getTradeCustomerGroupsId()
    {
        $customerGroupsString = $this->scopeConfig->getValue(self::XML_PATH_PERSPECTIVE_TRADE_CUSTOMER_GROUP, ScopeInterface::SCOPE_STORE);

        try {
            $customerGroupsArray = explode(',', $customerGroupsString);
        } catch (\Exception $e) {
            $customerGroupsArray = '';
        }

        if (!is_array($customerGroupsArray) || empty($customerGroupsArray)) {
            return '';
        }

        return implode(',', $customerGroupsArray);
    }

    /**
     * @return int
     */
    public function getQtyOfProductsForTrade(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_PERSPECTIVE_TRADE_QTY_OF_PRODUCTS, ScopeInterface::SCOPE_STORE);
    }

    public function getAllowedShippingForTrade()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PERSPECTIVE_TRADE_ALLOWED_SHIPPING, ScopeInterface::SCOPE_STORE);
    }

    public function getAllowedPaymentForTrade()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PERSPECTIVE_TRADE_ALLOWED_PAYMENT, ScopeInterface::SCOPE_STORE);
    }

    public function getLargeTradeCustomerGroupsId()
    {
        $customerGroupsString = $this->scopeConfig->getValue(self::XML_PATH_PERSPECTIVE_LARGE_TRADE_CUSTOMER_GROUP, ScopeInterface::SCOPE_STORE);

        try {
            $customerGroupsArray = explode(',', $customerGroupsString);
        } catch (\Exception $e) {
            $customerGroupsArray = '';
        }

        if (!is_array($customerGroupsArray) || empty($customerGroupsArray)) {
            return '';
        }

        return implode(',', $customerGroupsArray);
    }

    public function getAmountForFreeShippingForLargeTrade(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_PERSPECTIVE_LARGE_TRADE_AMOUNT_FOR_FREE_SHIPPING, ScopeInterface::SCOPE_STORE);
    }
    public function getAllowedPaymentForLargeTrade()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PERSPECTIVE_LARGE_TRADE_ALLOWED_PAYMENT, ScopeInterface::SCOPE_STORE);
    }
}
