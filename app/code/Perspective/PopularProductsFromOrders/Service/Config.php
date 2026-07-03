<?php
declare(strict_types=1);

namespace Perspective\PopularProductsFromOrders\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * `Service Config.
 */
class Config
{
    /**
     * Paths to Configurations.
     */
    private const XML_PATH_PERSPECTIVE_POPULAR_PRODUCTS_ENABLED = 'perspective_popular_products/general_settings/enabled';
    private const XML_PATH_PERSPECTIVE_POPULAR_PRODUCTS_CRON_FREQUENCY_UPDATE = 'perspective_popular_products/general_settings/update_frequency';
    private const XML_PATH_PERSPECTIVE_POPULAR_PRODUCTS_DISPLAY_COUNT = 'perspective_popular_products/general_settings/display_count';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

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
        return $this->scopeConfig->isSetFlag($this::XML_PATH_PERSPECTIVE_POPULAR_PRODUCTS_ENABLED);
    }

    /**
     * @return int
     */
    public function getDisplayCount(): int
    {
        return (int)$this->scopeConfig->getValue($this::XML_PATH_PERSPECTIVE_POPULAR_PRODUCTS_DISPLAY_COUNT);
    }
}
