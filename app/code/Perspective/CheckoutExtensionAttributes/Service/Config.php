<?php

declare(strict_types=1);

namespace Perspective\CheckoutExtensionAttributes\Service;

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
    private const XML_PATH_CUSTOMER_REFERENCE_GENERAL_IS_VISIBLE = 'customer_reference/general_settings/is_visible';


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
    public function isVisible(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_CUSTOMER_REFERENCE_GENERAL_IS_VISIBLE, ScopeInterface::SCOPE_STORE);
    }
}
