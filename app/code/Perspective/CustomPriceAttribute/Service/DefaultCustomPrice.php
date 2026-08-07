<?php
declare(strict_types=1);

namespace Perspective\CustomPriceAttribute\Service;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * DefaultCustomPrice Service.
 */
class DefaultCustomPrice
{
    /**
     * Paths to Configurations.
     */
    public const string XML_PATH_CUSTOM_PRICE_INCREASE_PERCENTAGE = 'custom_price_attribute/general_settings/price_increase_percentage';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * DefaultCustomPrice constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return int
     */
    public function getPriceIncreasePercentage(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_CUSTOM_PRICE_INCREASE_PERCENTAGE);
    }
    /**
     * @param $object
     * @return float
     */
    public function getDefaultCustomPrice($object): float
    {
        $price = $object->getData('price');

        if (!$price) {
            /** @var Configurable $typeInstance */
            $typeInstance = $object->getTypeInstance();
            $childProducts = $typeInstance->getUsedProducts($object);

            $prices = [];

            foreach ($childProducts as $child) {
                $childPrice = (float)$child->getFinalPrice();
                if ($childPrice > 0) {
                    $prices[] = $childPrice;
                }
            }

            if (!empty($prices)) {
                $price = min($prices);
            }
        }

        $priceIncreasePercentage = $this->getPriceIncreasePercentage();
        return (float)$price * (1 + $priceIncreasePercentage / 100);
    }

    /**
     * @param $object
     * @return bool
     */
    public function isDefaultCustomPrice($object): bool
    {
        $price = $object->getData('custom_price');
        if ($price === null) {
            return true;
        }
        $price = (float)$price;
        $roundedPrice = round($price, 2, PHP_ROUND_HALF_UP);
        $roundedDefaultPrice = round($this->getDefaultCustomPrice($object), 2, PHP_ROUND_HALF_UP);

        return $roundedDefaultPrice === $roundedPrice;
    }
}
