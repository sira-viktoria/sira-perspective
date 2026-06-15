<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Block;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Widget\Block\BlockInterface;

/**
 * SoldProducts Widget.
 */
class SoldProducts extends Template implements BlockInterface
{
    private const DEFAULT_PRODUCT_LIMIT = 4;

    /**
     * @var CatalogHelper
     */
    //Test. Incorrect variable name in SonarQube error checking
    protected CatalogHelper $_catalogHelper;

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
     * SoldProducts constructor.
     *
     * @param Context $context
     * @param CatalogHelper $catalogHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        CatalogHelper $catalogHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return Product|null
     */
    public function getCurrentProduct(): ?Product
    {
        return $this->catalogHelper->getProduct();
    }

    /**
     * @return array|ProductInterface[]
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

        $orders = $this->getLastOrders();

        return $this->getLastSoldProducts($orders);
    }

    /**
     * @return OrderInterface[]
     */
    public function getLastOrders(): array
    {
        $dateLimit = date('Y-m-d H:i:s', strtotime('-1 day'));

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

        $orderSearchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$dateFilter])
            ->addFilters([$statusFilter])
            ->create();

       return $this->orderRepository->getList($orderSearchCriteria)->getItems();
    }

    /**
     * @param $orders
     * @return array|ProductInterface[]
     */
    public function getLastSoldProducts($orders): array
    {
        $currentProduct = $this->getCurrentProduct();
        $soldProductIds = [];
        foreach ($orders as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $productId = $item->getProductId();
                if ($productId != $currentProduct->getId()) {
                    $soldProductIds[] = $productId;
                }
            }
        }

        if (empty($soldProductIds)) {
            return [];
        }

        $soldProductIds = array_unique($soldProductIds);
        $limit = (int)$this->getData('products_number') ?: self::DEFAULT_PRODUCT_LIMIT;

        $idFilter = $this->filterBuilder
            ->setField('entity_id')
            ->setConditionType('in')
            ->setValue($soldProductIds)
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

        $productSearchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$idFilter])
            ->addFilters([$categoryFilter])
            ->addFilters([$statusFilter])
            ->setPageSize($limit)
            ->create();

        return $this->productRepository->getList($productSearchCriteria)->getItems();
    }
}
