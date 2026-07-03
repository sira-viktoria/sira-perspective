<?php
declare(strict_types=1);

namespace Perspective\PopularProductsFromOrders\Service;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Perspective\PopularProductsFromOrders\Model\PopularProductFactory;
use Perspective\PopularProductsFromOrders\Model\ResourceModel\PopularProduct as ResourceModel;

/**
 * PopularProductsCollector Service.
 */
class PopularProductsCollector
{
    /**
     * @var OrderCollectionFactory
     */
    protected OrderCollectionFactory $orderCollectionFactory;

    /**
     * @var OrderItemCollectionFactory
     */
    protected OrderItemCollectionFactory $orderItemCollectionFactory;

    /**
     * @var Config
     */
    protected Config $configDataService;

    /**
     * @var PopularProductFactory
     */
    protected PopularProductFactory $popularProductFactory;

    /**
     * @var ResourceModel
     */
    protected ResourceModel $resourceModel;

    /**
     * PopularProductsCollector constructor.
     *
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param Config $configDataService
     * @param PopularProductFactory $popularProductFactory
     * @param ResourceModel $resourceModel
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        Config $configDataService,
        PopularProductFactory $popularProductFactory,
        ResourceModel $resourceModel,
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->configDataService = $configDataService;
        $this->popularProductFactory = $popularProductFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * Get a sorted list of product ids and how many times they were ordered.
     *
     * @return array [product_id => count]
     */
    private function getProductFrequencyData(): array
    {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToSelect(['entity_id', 'status'])
            ->addFieldToFilter('status', ['in' => ['processing', 'complete']]);
        $orderIds = $orderCollection->getColumnValues('entity_id');

        $itemCollection = $this->orderItemCollectionFactory->create()
            ->addFieldToSelect(['product_id'])
            ->addFieldToFilter('order_id', ['in' => $orderIds])
            ->addFieldToFilter('parent_item_id', ['null' => true]);
        $productIds = $itemCollection->getColumnValues('product_id');

        return array_count_values($productIds);
    }

    /**
     * Get the top popular products based on the limit from settings.
     *
     * @return array [product_id => count]
     */
    public function getTopProductStats(): array
    {
        $productCounts = $this->getProductFrequencyData();
        if (empty($productCounts)) {
            return [];
        }

        $limit = $this->configDataService->getDisplayCount();
        arsort($productCounts);
        return array_slice($productCounts, 0, $limit, true);
    }
}
