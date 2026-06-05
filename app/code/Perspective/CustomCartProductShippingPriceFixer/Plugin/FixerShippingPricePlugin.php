<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomCartProductShippingPriceFixer\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\Serialize\SerializerInterface;
use Perspective\CustomCartProductShipping\Model\Carrier\Shipping;
use Perspective\CustomCartProductShippingPriceFixer\Model\Config;

/**
 * FixerShippingPricePlugin Plugin.
 */
class FixerShippingPricePlugin
{
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * ApplyDynamicDiscount constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param Config $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface  $serializer,
        Config $config
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    /**
     * @param Shipping $subject
     * @param $result
     * @param RateRequest $request
     * @return mixed
     */
    public function afterCollectRates(Shipping $subject, $result, RateRequest $request): mixed
    {
        if ($this->config->isEnabled() && $result && $result->getAllRates()) {
            $priceForUpdateFromConfig = $this->config->getShippingCostForUpdate();

            foreach ($result->getAllRates() as $rate) {
                if ($rate->getPrice() <= $priceForUpdateFromConfig) {
                    $rate->setPrice(ceil($priceForUpdateFromConfig));
                    $rate->setCost(ceil($priceForUpdateFromConfig));
                }
            }
        }

        return $result;
    }
}
