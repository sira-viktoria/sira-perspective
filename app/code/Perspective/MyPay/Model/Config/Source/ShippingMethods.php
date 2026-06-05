<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\MyPay\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config as ShippingConfig;

/**
 * ShippingMethods Source Model.
 */
class ShippingMethods implements OptionSourceInterface
{
    /**
     * @var ShippingConfig
     */
    protected ShippingConfig $shippingConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * ShippingMethods constructor.
     *
     * @param ShippingConfig $shippingConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ShippingConfig $shippingConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->shippingConfig = $shippingConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return array of carriers and their carrier methods.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $activeCarriers = $this->shippingConfig->getActiveCarriers();

        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }

            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $options[] = [
                    'value' => $carrierCode,
                    'label' => __($methodTitle . ' ('. $methodCode . ')')
                ];
            }
        }

        return $options;
    }
}
