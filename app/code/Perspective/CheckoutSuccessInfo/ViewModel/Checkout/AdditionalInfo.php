<?php
declare(strict_types=1);

namespace Perspective\CheckoutSuccessInfo\ViewModel\Checkout;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Perspective\CheckoutExtensionAttributes\Api\ReferenceStorageInterface;

/**
 * AdditionalInfo ViewModel.
 */
readonly class AdditionalInfo implements ArgumentInterface
{
    /**
     * AdditionalInfo constructor.
     *
     * @param ReferenceStorageInterface $storage
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        private ReferenceStorageInterface $storage,
        private OrderRepositoryInterface $orderRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    /**
     * @param string  $orderNumber
     *
     * @return string|null
     */
    public function getCustomerReference(string $orderNumber): ?string
    {
        $order = $this->getOrderByNumber($orderNumber);

        return $this->storage->get((int) $order->getQuoteId()) ?? null;
    }

    /**
     * @param string $orderNumber
     *
     * @return string|null
     */
    public function getDeliveryComment(string $orderNumber): ?string
    {
        $order = $this->getOrderByNumber($orderNumber);

        return $order->getData('order_delivery_comment') ?? null;
    }

    /**
     * @param string $orderNumber
     *
     * @return string|null
     */
    public function getOrderStatus(string $orderNumber): ?string
    {
        $order = $this->getOrderByNumber($orderNumber);

        return $order->getStatus() ?? null;
    }

    /**
     * @param string $orderNumber
     *
     * @return OrderInterface|null
     */
    public function getOrderByNumber(string $orderNumber): ?OrderInterface
    {

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $orderNumber)
            ->create();

        $orderList = $this->orderRepository->getList($searchCriteria);

        foreach ($orderList->getItems() as $order) {
            return $order;
        }

        return null;
    }
}
