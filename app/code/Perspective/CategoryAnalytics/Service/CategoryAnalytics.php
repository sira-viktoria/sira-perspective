<?php
declare(strict_types=1);

namespace Perspective\CategoryAnalytics\Service;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

/**
 * CategoryAnalytics Class.
 */
class CategoryAnalytics
{
    /**
     * @var CacheInterface
     */
    protected CacheInterface $cache;

    protected PriceHelper $priceHelper;

    /**
     * @var CategoryProductCollection
     */
    protected CategoryProductCollection $productCollectionService;

    /**
     * CategoryAnalytics constructor.
     *
     * @param CacheInterface $cache
     * @param PriceHelper $priceHelper
     * @param CategoryProductCollection $productCollectionService
     */
    public function __construct(
        CacheInterface $cache,
        PriceHelper $priceHelper,
        CategoryProductCollection $productCollectionService
    ) {
        $this->cache = $cache;
        $this->priceHelper = $priceHelper;
        $this->productCollectionService = $productCollectionService;
    }

    /**
     * Collect analytics data for a category
     * Uses cache if available, otherwise collect values from product collections
     * cache_id => CATEGORY_ANALYTICS_categoryId_storeId
     *
     * @param Category $category
     * @return array
     */
    public function getAnalytics(Category $category): array
    {
        $cacheId = sprintf(
            'CATEGORY_ANALYTICS_%d_%d',
            $category->getId(),
            $category->getStoreId()
        );

        $data = $this->cache->load($cacheId);
        if ($data) {
            return unserialize($data);
        }

        $data = [
            'total_count' => $this->getCategorySize($category, false),
            'average_price' => $this->getCategoryAveragePrice($category),
            'in_stock_count' => $this->getCategorySize($category, true)
        ];

        $this->cache->save(serialize($data), $cacheId, ['category_analytics'], 1800);

        return $data;
    }

    /**
     * Get size of category product collection.
     *
     * @param Category $category
     * @param bool $stockFilter
     * @return int
     */
    protected function getCategorySize(Category $category, bool $stockFilter): int
    {
        return $this->productCollectionService->getCategoryProductCollection($category, $stockFilter)->getSize();
    }

    /**
     * Get average price from category product collection.
     *
     * @param Category $category
     * @return float|string
     */
    public function getCategoryAveragePrice(Category $category): float|string
    {
        $prices = $this->productCollectionService->getCategoryProductCollection($category, true)->getColumnValues('price');
        if (empty($prices)) {
            return 0.0;
        }
        $value = round(array_sum($prices)/count($prices), 2);

        return $this->priceHelper->currency($value, true, false);
    }
}
