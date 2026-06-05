<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomCartProductShippingPriceFixer\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Config Model.
 */
class Config
{
    /**
     * Paths to Configurations.
     */
    private const XML_PATH_UPDATE_MIN_SHIPPING_COST = 'shipping/perspectiveshipping/update_price/min_shipping_cost';
    private const XML_PATH_UPDATE_MIN_SHIPPING_COST_ENABLED= 'shipping/perspectiveshipping/update_price/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_UPDATE_MIN_SHIPPING_COST_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return float
     */
    public function getShippingCostForUpdate(): float
    {
        return (float)$this->scopeConfig->getValue(self::XML_PATH_UPDATE_MIN_SHIPPING_COST, ScopeInterface::SCOPE_STORE);
    }
}
