<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Block;

use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Widget\Block\BlockInterface;
use Perspective\SoldTodayWidget\Api\Data\OrderDataInterface;
use Perspective\SoldTodayWidget\Api\OrderDataRepositoryInterface;
use Perspective\SoldTodayWidget\Model\ResourceModel\OrderData\CollectionFactory;

/**
 * SoldProducts Widget.
 */
class SoldProducts extends Template implements BlockInterface
{
    private const DEFAULT_PRODUCT_LIMIT = 4;

    /**
     * @var CatalogHelper
     */
    protected CatalogHelper $catalogHelper;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected FilterBuilder $filterBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var ImageHelper
     */
    protected ImageHelper $imageHelper;

    /**
     * @var OrderDataRepositoryInterface
     */
    protected OrderDataRepositoryInterface $orderDataRepository;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $orderDataCollectionFactory;

    /**
     * @var SortOrderBuilder
     */
    protected SortOrderBuilder $sortOrderBuilder;

    /**
     * SoldProducts constructor.
     *
     * @param Context $context
     * @param CatalogHelper $catalogHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param ProductRepositoryInterface $productRepository
     * @param OrderDataRepositoryInterface $orderDataRepository
     * @param CollectionFactory $orderDataCollectionFactory
     * @param SortOrderBuilder $sortOrderBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        CatalogHelper $catalogHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        OrderDataRepositoryInterface $orderDataRepository,
        CollectionFactory $orderDataCollectionFactory,
        SortOrderBuilder $sortOrderBuilder,
        array $data = []
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->orderDataRepository = $orderDataRepository;
        $this->orderDataCollectionFactory = $orderDataCollectionFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @return Product|OrderInterface|null
     * @throws NoSuchEntityException
     */
    public function getCurrentProduct(): OrderInterface|Product|null
    {
        $currentProduct =  $this->catalogHelper->getProduct();

        if (!$currentProduct && $this->getCurrentProductId()) {
            $currentProduct = $this->productRepository->getById($this->getCurrentProductId());
        }
        return $currentProduct;
    }

    /**
     * @return array|ProductInterface[]
     * @throws NoSuchEntityException
     */
    public function getSoldProducts(): array
    {
        $currentProduct = $this->getCurrentProduct();
        if (!$currentProduct || !$currentProduct->getId()) {
            return [];
        }

        $categoryIds = $currentProduct->getCategoryIds();
        if (empty($categoryIds)) {
            return [];
        }

        return $this->getLastSoldProducts();
    }

    /**
     * @return OrderInterface[]
     */
    public function getLastOrders(): array
    {
        $dateLimit = date('Y-m-d H:i:s', strtotime('-2 day'));

        $dateFilter = $this->filterBuilder
            ->setField('created_at')
            ->setConditionType('gteq')
            ->setValue($dateLimit)
            ->create();

        $statusFilter = $this->filterBuilder
            ->setField('status')
            ->setConditionType('in')
            ->setValue(['processing', 'complete', 'pending'])
            ->create();

        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $orderSearchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$dateFilter])
            ->addFilters([$statusFilter])
            ->setSortOrders([$sortOrder])
            ->create();

        return $this->orderRepository->getList($orderSearchCriteria)->getItems();
    }

    /**
     * @return array|ProductInterface[]
     * @throws NoSuchEntityException
     */
    public function getLastSoldProducts(): array
    {
        $lastSoldProductIds = $this->getAllVisibleItemsFromLastOrders();

        if (empty($lastSoldProductIds)) {
            return [];
        }

        $currentProduct = $this->getCurrentProduct();
        $lastSoldProductIds = array_unique($lastSoldProductIds);
        $limit = (int)$this->getData('products_number') ?: self::DEFAULT_PRODUCT_LIMIT;

        $idFilter = $this->filterBuilder
            ->setField('entity_id')
            ->setConditionType('in')
            ->setValue($lastSoldProductIds)
            ->create();

        $categoryFilter = $this->filterBuilder
            ->setField('category_id')
            ->setConditionType('in')
            ->setValue($currentProduct->getCategoryIds())
            ->create();

        $statusFilter = $this->filterBuilder
            ->setField('status')
            ->setConditionType('eq')
            ->setValue(Status::STATUS_ENABLED)
            ->create();

        $visibleFilter = $this->filterBuilder
            ->setField('visibility')
            ->setConditionType('eq')
            ->setValue(Visibility::VISIBILITY_BOTH)
            ->create();

        $productSearchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$idFilter])
            ->addFilters([$categoryFilter])
            ->addFilters([$statusFilter])
            ->addFilters([$visibleFilter])
            ->setPageSize($limit)
            ->create();

        return $this->productRepository->getList($productSearchCriteria)->getItems();
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAllVisibleItemsFromLastOrders(): array
    {
        $limit = (int)$this->getData('products_number') ?: self::DEFAULT_PRODUCT_LIMIT;;
        $lastOrders = $this->getLastOrders();
        $soldProductIds = [];

        //If count of orders is less than limit - Get orders from magento sales table
        if(!empty($lastOrders) && count($lastOrders) <= $limit) {
            $currentProduct = $this->getCurrentProduct();
            foreach ($lastOrders as $order) {
                $items = $order->getItems();
                foreach ($items as $item) {
                    if ($item->getProductId() != $currentProduct->getId()) {
                        $soldProductIds[] = $item->getProductId();
                    }
                }
            }
        }

        //If count of orders is more than limit - get data from perspective custom table
        if(count($lastOrders) > $limit) {
            $dateLimit = date('Y-m-d H:i:s', strtotime('-2 day'));

            $orderCollection = $this->orderDataCollectionFactory->create();
            $orderCollection->addFieldToSelect(
                [
                    OrderDataInterface::ORDER_ID,
                    OrderDataInterface::PRODUCT_ID,
                    OrderDataInterface::CREATED_AT
                ]);
            $orderCollection->addFieldToFilter( OrderDataInterface::CREATED_AT, ['gteq' => $dateLimit]);
            $orderCollection->setPageSize(500);

            $lastPageNumber = $orderCollection->getLastPageNumber();

            for ($i = 1; $i <= $lastPageNumber; $i++) {
                $orderCollection->setCurPage($i);

                foreach ($orderCollection as $item) {
                    $soldProductIds[]  = $item[OrderDataInterface::PRODUCT_ID];
                }

                $orderCollection->clear();
            }
        }
        return $soldProductIds;
    }
}
