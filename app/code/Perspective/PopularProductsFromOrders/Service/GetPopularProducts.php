<?php
declare(strict_types=1);

namespace Perspective\PopularProductsFromOrders\Service;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Perspective\PopularProductsFromOrders\Model\PopularProductFactory;
use Perspective\PopularProductsFromOrders\Model\ResourceModel\PopularProduct as ResourceModel;
use Perspective\PopularProductsFromOrders\Model\ResourceModel\PopularProduct\CollectionFactory as PopularCollectionFactory;
use Psr\Log\LoggerInterface;
use Throwable;
use Zend_Db_Expr;

/**
 * GetPopularProducts Service.
 */
class GetPopularProducts
{
    /**
     * @var PopularProductsCollector
     */
    protected PopularProductsCollector $popularProductsCollector;

    /**
     * @var PopularProductFactory
     */
    protected PopularProductFactory $popularProductFactory;

    /**
     * @var ResourceModel
     */
    protected ResourceModel $resourceModel;

    /**
     * @var PopularCollectionFactory
     */
    protected PopularCollectionFactory $popularCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected ProductCollectionFactory $productCollectionFactory;

    /**
     * @var Config
     */
    protected $configDataService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * GetPopularProducts constructor.
     *
     * @param PopularProductsCollector $popularProductsCollector
     * @param PopularProductFactory $popularProductFactory
     * @param ResourceModel $resourceModel
     * @param PopularCollectionFactory $popularCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Config $configDataService
     * @param LoggerInterface $logger
     */
    public function __construct(
        PopularProductsCollector $popularProductsCollector,
        PopularProductFactory $popularProductFactory,
        ResourceModel $resourceModel,
        PopularCollectionFactory $popularCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        Config $configDataService,
        LoggerInterface $logger
    ) {
        $this->popularProductsCollector = $popularProductsCollector;
        $this->popularProductFactory = $popularProductFactory;
        $this->resourceModel = $resourceModel;
        $this->popularCollectionFactory = $popularCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->configDataService = $configDataService;
        $this->logger = $logger;
    }

    /**
     * Update popular product ranks
     *
     * @return void
     */
    public function refreshTopProducts(): void
    {
        $topProducts = $this->popularProductsCollector->getTopProductStats();
        $rank = 1;
        foreach ($topProducts as $productId => $count) {
            try {
                $model = $this->popularProductFactory->create();

                $this->resourceModel->load($model, $rank);

                $model->setData('rank', $rank);
                $model->setData('product_id', $productId);
                $model->setData('orders_count', $count);

                $this->resourceModel->save($model);
            } catch (Throwable $e) {
                $this->logger->error(__('Failed saving popular product (ID: %1, rank: %2): %3',
                    $productId,
                    $rank,
                    $e->getMessage()));
            }
            $rank++;
        }
        $this->deleteInvalidRanks();
    }

    /**
     * Get popular products array sorted by rank
     *
     * @return array
     */
    public function getTopProducts(): array
    {
        $popularCollection = $this->popularCollectionFactory->create();
        $productIds = $popularCollection->getColumnValues('product_id');

        $productCollection = $this->productCollectionFactory->create()
            ->addIdFilter($productIds)
            ->addAttributeToSelect('*');

        if (!empty($productIds)) {
            $productCollection->getSelect()->order(new Zend_Db_Expr('FIELD(entity_id,' . implode(',', $productIds).')'));
        }

        return $productCollection->getItems();
    }

    /**
     * Remove records from popular products table if they rank greater than limit
     *
     * @return void
     */
    public function deleteInvalidRanks(): void
    {
        $limit = $this->configDataService->getDisplayCount();

        $collection = $this->popularCollectionFactory->create();
        $collection->addFieldToFilter('rank', ['gt' => $limit]);

        foreach ($collection as $record) {
            try {
                $record->delete();
            } catch (Throwable $e) {
                $this->logger->error(__('Failed deleting popular product (ID: %1, rank: %2): %3',
                    $record->getProductId(),
                    $record->getRank(),
                    $e->getMessage()));
            }
        }
    }
}
