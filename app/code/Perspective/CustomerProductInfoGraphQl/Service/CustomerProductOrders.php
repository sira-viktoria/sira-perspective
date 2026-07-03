<?php
declare(strict_types=1);

namespace Perspective\CustomerProductInfoGraphQl\Service;

use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

/**
 * CustomerProductOrders Class.
 */
class CustomerProductOrders
{
    /**
     * @var null
     */
    protected $productId = null;

    /**
     * @var null
     */
    protected $ordersWithProduct = null;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var TimezoneInterface
     */
    protected TimezoneInterface $timezone;

    /**
     * @var Configurable
     */
    protected Configurable $configurableTypeResourceModel;

    /**
     * @var CurrentCustomer
     */
    protected CurrentCustomer $currentCustomerService;

    /**
     * CustomerProductOrders constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezone
     * @param Configurable $configurableTypeResourceModel
     * @param CurrentCustomer $currentCustomerService
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezone,
        Configurable $configurableTypeResourceModel,
        CurrentCustomer $currentCustomerService
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->configurableTypeResourceModel = $configurableTypeResourceModel;
        $this->currentCustomerService = $currentCustomerService;
    }

    /**
     * Set product ID.
     *
     * @param $productId
     *
     * @return void
     */
    public function setProductId($productId): void
    {
        if ($this->productId !== $productId) {
            $this->ordersWithProduct = null;
        }
        $this->productId = $productId;
    }

    /**
     * Get order collection for the current customer excluding canceled orders.
     *
     * @return Collection
     */
    public function getCustomerOrders(): Collection
    {
        return $this->collectionFactory->create()
            ->addFieldToSelect(['entity_id', 'status', 'created_at'])
            ->addFieldToFilter('customer_id', $this->currentCustomerService->getCustomerId())
            ->addFieldToFilter('status', ['neq' => 'canceled'])
            ->setOrder('created_at', 'DESC');
    }

    /**
     * Get orders that contain the current product
     * If item is a child of a configurable product, the parent ID is used for comparison
     *
     * @return array
     */
    public function getCustomerOrdersWithProduct(): array
    {
        if ($this->ordersWithProduct === null) {
            $orders = $this->getCustomerOrders();

            $ordersWithProduct = [];
            foreach ($orders as $order) {
                $items = $order->getAllItems();
                foreach ($items as $item) {
                    $itemProductId = $item->getProductId();

                    $parentProductId = $this->configurableTypeResourceModel->getParentIdsByChild($itemProductId);
                    if ($parentProductId) {
                        $itemProductId = $parentProductId[0];
                    }

                    if ($itemProductId == $this->productId) {
                        $ordersWithProduct[] = $order;
                        break;
                    }
                }
            }
            $this->ordersWithProduct = $ordersWithProduct;
        }
        return $this->ordersWithProduct;
    }

    /**
     * Get total count of orders containing the current product
     *
     * @return int
     */
    public function getProductOrdersCount(): int
    {
        return sizeof($this->getCustomerOrdersWithProduct());
    }

    /**
     * Check if customer has previously ordered the current product.
     *
     * @return bool
     */
    public function isCustomerOrderedProduct(): bool
    {
        return $this->getProductOrdersCount() > 0;
    }

    /**
     * Get formatted date of the last order containing this product.
     *
     * @return string
     */
    public function getLastPurchaseDate(): string
    {
        $orders = $this->getCustomerOrdersWithProduct();
        if (empty($orders)) {
            return '';
        }
        $latestOrder = reset($orders);
        return $this->timezone->formatDateTime($latestOrder->getCreatedAt());
    }
}
