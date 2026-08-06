<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;
use Magento\InventorySalesApi\Api\Data\IsProductSalableResultInterface;

/**
 * BonusValidator Class.
 */
class BonusValidator
{
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;
    /**
     * @var AreProductsSalableInterface
     */
    protected AreProductsSalableInterface $productSalableInterface;

    /**
     * BonusValidator constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param AreProductsSalableInterface $productSalableInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AreProductsSalableInterface $productSalableInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productSalableInterface = $productSalableInterface;
    }


    /**
     * @param $quote
     * @return bool
     */
    public function isCartRulesApplied($quote): bool
    {
        if ($quote->getBaseSubtotal() == $quote->getBaseSubtotalWithDiscount()) {
            return false;
        }
        return true;
    }

    /**
     * @param $bonus_code
     * @return bool
     */
    public function isBonusEnabled($bonus_code): bool
    {
        return $this->scopeConfig->isSetFlag(Config::XML_PATH .  $bonus_code . '/enabled');
    }

    /**
     * @param $bonus_code
     * @return mixed
     */
    public function getBonusConfig($bonus_code): mixed
    {
        return $this->scopeConfig->getValue(Config::XML_PATH . $bonus_code);
    }

    /**
     * @param $sku
     * @return IsProductSalableResultInterface[]
     */
    public function isProductSalable($sku): array
    {
        return $this->productSalableInterface->execute([$sku], 1);
    }
}
