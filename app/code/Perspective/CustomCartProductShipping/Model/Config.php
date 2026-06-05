<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomCartProductShipping\Model;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Config Model.
 */
class Config
{
    public const PERSPECTIVE_SHIPPING_CODE = 'perspectiveshipping';

    /**
     * Paths to Configurations.
     */
    private const XML_PATH_ENABLED = 'shipping/perspectiveshipping/general/enabled';
    private const XML_PATH_AVAILABLE_CUSTOMER_GROUPS = 'shipping/perspectiveshipping/general/available_customer_groups';
    private const XML_PATH_MAX_NUMBER_OF_PRODUCTS= 'shipping/perspectiveshipping/general/max_number_of_products';
    private const XML_PATH_MAX_DISCOUNT = 'shipping/perspectiveshipping/general/max_discount';
    private const XML_PATH_DISCOUNT_RULES = 'shipping/perspectiveshipping/general/discount_rules';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param Cart $cart
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        Cart $cart
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->cart = $cart;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getMaxNumberOfProducts(): int
    {
        return (int)$this->scopeConfig->isSetFlag(self::XML_PATH_MAX_NUMBER_OF_PRODUCTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getDiscountRules(): array
    {
        $rulesJson = $this->scopeConfig->getValue(self::XML_PATH_DISCOUNT_RULES, ScopeInterface::SCOPE_STORE);

        try {
            $rules = $this->serializer->unserialize($rulesJson);
        } catch (\Exception $e) {
            $rules = [];
        }

        if (!is_array($rules) || empty($rules)) {
            return [];
        }

        usort($rules, function ($a, $b) {
            return (int)$b['qty'] <=> (int)$a['qty'];
        });

        return $rules;
    }

    /**
     * @return mixed
     */
    public function getDefaultShippingPrice(): mixed
    {
        $path = 'carriers/' . self::PERSPECTIVE_SHIPPING_CODE. '/' . 'price';

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return float
     */
    public function getShippingPriceWithDiscount(): float
    {
        $priceWithMaxDiscount = $this->getPriceWithMaxDiscount();
        if ($priceWithMaxDiscount) {
            return $priceWithMaxDiscount;
        }

        $defaultShippingPrice = $this->getDefaultShippingPrice();
        $totalItemsQty = $this->cart->getQuote()->getItemsSummaryQty() ? $this->cart->getQuote()->getItemsQty() : 0;

        $rules = $this->getDiscountRules();

        foreach ($rules as $rule) {
            if ($totalItemsQty >= (int)$rule['qty']) {
                return (float)$defaultShippingPrice - (float)$rule['discount'] / 100 * (float)$defaultShippingPrice;
            }
        }

        return 0;
    }

    /**
     * @return float|int
     */
    public function getPriceWithMaxDiscount(): float|int
    {
        $maxNumberOfProducts = $this->scopeConfig->getValue(self::XML_PATH_MAX_NUMBER_OF_PRODUCTS, ScopeInterface::SCOPE_STORE);
        $maxDiscount = $this->scopeConfig->getValue(self::XML_PATH_MAX_DISCOUNT, ScopeInterface::SCOPE_STORE);
        $priceWithMaxDiscount = 0;

        $totalItemsQty = $this->cart->getQuote()->getItemsSummaryQty() ? $this->cart->getQuote()->getItemsQty() : 0;
        $defaultShippingPrice = $this->getDefaultShippingPrice();
        if ($maxNumberOfProducts && $totalItemsQty >= $maxNumberOfProducts)  {
            $priceWithMaxDiscount = (float)$defaultShippingPrice - (float)$maxDiscount / 100 * (float)$defaultShippingPrice;
        }

        return $priceWithMaxDiscount;
    }

    /**
     * @return array
     */
    public function getAvailableCustomerGroupsId(): array
    {
        $customerGroupsString = $this->scopeConfig->getValue(self::XML_PATH_AVAILABLE_CUSTOMER_GROUPS, ScopeInterface::SCOPE_STORE);

        try {
            $customerGroupsArray = explode(',', $customerGroupsString);
        } catch (\Exception $e) {
            $customerGroupsArray = [];
        }

        if (!is_array($customerGroupsArray) || empty($customerGroupsArray)) {
            return [];
        }

        return $customerGroupsArray;
    }
}
