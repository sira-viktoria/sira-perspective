<?php
declare(strict_types=1);

namespace Perspective\CategoryAnalytics\Service;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;

/**
 * CategoryProductCollection Class Service.
 */
class CategoryProductCollection
{
    protected array $collectionsCache = [];

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $productCollectionFactory;

    /**
     * @var Stock
     */
    protected Stock $stockFilter;

    /**
     * CategoryProductCollection Constructor.
     *
     * @param CollectionFactory $productCollectionFactory
     * @param Stock $stockFilter
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        Stock $stockFilter
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockFilter = $stockFilter;
    }

    /**
     * Get product collection for a category
     * Filtered by visibility, status, stock, categories
     *
     * @param Category $category
     * @param bool $stockFilter
     * @return Collection
     */
    public function getCategoryProductCollection(Category $category, bool $stockFilter): Collection
    {
        $collectionType = 'all';
        if ($stockFilter) {
            $collectionType = 'in_stock';
        }

        $collection = $this->productCollectionFactory->create();
        $collection->setStoreId($category->getStoreId());

        if (!$stockFilter) {
            $collection->setFlag('has_stock_status_filter', false);
        }
        if ($stockFilter) {
            $this->stockFilter->addInStockFilterToCollection($collection);
        }

        $collection->addAttributeToSelect(['entity_id', 'price']);

        $categoryIds = $category->getChildren(true);
        if (!$categoryIds) {
            $categoryIds = [$category->getId()];
        }
        $collection->addCategoriesFilter(['in' => $categoryIds]);

        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH);

        $collection->load();

        $this->collectionsCache[$collectionType] = $collection;
        return $collection;
    }
}
