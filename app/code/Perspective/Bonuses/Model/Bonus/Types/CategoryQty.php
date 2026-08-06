<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model\Bonus\Types;

use Magento\Framework\Exception\NoSuchEntityException;
use Perspective\Bonuses\Model\Bonus\AbstractBonus;

/**
 * CategoryQty Types.
 */
class CategoryQty extends AbstractBonus
{
    public const string BONUS_CODE = 'discount_category';
    public const string MESSAGE_TEMPLATE = 'Bonus: %d%% discount for %d items from category %s';
    protected array $categoryItemsQtyArray = [];

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     */
    public function isApplicable($quote, $total): bool
    {
        if ($this->bonusValidator->isCartRulesApplied($quote)) {
            return false;
        }

        if (!$this->isEnabled()){
            return false;
        }

        $config = $this->getConfig();
        if ($config['category_id'] == null &&
            $config['percent'] == null &&
            $config['min_qty'] == null
        ) {
            return false;
        }

        $applicableCategoryIds = $this->config->stringToArray($config['category_id']);
        $minCategoryQty = $config['min_qty'];
        $categoryItemsQtyArray = [];

        foreach ($quote->getItems() as $item) {
            $productCategoryIds = $this->config->getCategoryIdsByProductSku($item->getSku());
            $qty = $item->getQty();

            foreach (array_intersect($productCategoryIds, $applicableCategoryIds) as $categoryId) {
                if (!isset($categoryItemsQtyArray[$categoryId])) {
                    $categoryItemsQtyArray[$categoryId] = 0;
                }
                $categoryItemsQtyArray[$categoryId] += $qty;
            }
        }
        $this->categoryItemsQtyArray = $categoryItemsQtyArray;
        foreach ($categoryItemsQtyArray as $qty) {
            if ($qty >= $minCategoryQty) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     */
    public function apply($quote, $total): array
    {
        $applicableCategories = $this->getCategoryItemsQtyArray();
        if (empty($applicableCategories)) {
            return [];
        }

        $discountConfigValue = $this->getConfig()['percent'];
        $totalDiscount = 0;
        $frontendMessages = [];

        foreach ($applicableCategories as $categoryId => $qty) {
            $categoryDiscount = 0;
            foreach ($quote->getItems() as $item) {
                $productCategoryIds = $this->config->getCategoryIdsByProductSku($item->getSku());
                if (in_array($categoryId, $productCategoryIds)) {
                    $categoryDiscount += $item->getQty() * $item->getPrice() * ($discountConfigValue / 100);
                }
            }
            if ($categoryDiscount > 0) {
                $totalDiscount += $categoryDiscount;

                $categoryName = $this->config->getCategoryNameById($categoryId);
                $frontendMessages[] = sprintf(self::MESSAGE_TEMPLATE, $discountConfigValue, $qty, $categoryName);
            }
        }

        $total->addTotalAmount(self::BONUS_TOTAL_CODE, -$totalDiscount);
        $total->addBaseTotalAmount(self::BONUS_TOTAL_CODE, -$totalDiscount);

        return [
            'bonus_discount' => $totalDiscount,
            'bonus_messages' => $frontendMessages
        ];
    }

    /**
     * @return array
     */
    private function getCategoryItemsQtyArray(): array
    {
        $minCategoryQty = $this->getConfig()['min_qty'];

        return array_filter(
            $this->categoryItemsQtyArray,
            fn($qty) => $qty >= $minCategoryQty
        );
    }
}
