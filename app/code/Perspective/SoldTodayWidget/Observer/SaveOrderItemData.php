<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Perspective\SoldTodayWidget\Api\Data\OrderDataInterface;
use Perspective\SoldTodayWidget\Api\OrderDataRepositoryInterface;
use Perspective\SoldTodayWidget\Model\OrderDataFactory;

/**
 * SaveOrderItemData Observer.
 */
class SaveOrderItemData implements ObserverInterface
{
    /**
     * @var OrderDataFactory
     */
    protected OrderDataFactory $orderDataFactory;

    /**
     * @var OrderDataRepositoryInterface
     */
    protected OrderDataRepositoryInterface $orderDataRepository;

    /**
     * SaveOrderItemData constructor.
     *
     * @param OrderDataFactory $customDataFactory
     * @param OrderDataRepositoryInterface $orderDataRepository
     */
    public function __construct(
        OrderDataFactory $customDataFactory,
        OrderDataRepositoryInterface $orderDataRepository
    ) {
        $this->orderDataFactory = $customDataFactory;
        $this->orderDataRepository = $orderDataRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        foreach ($order->getAllVisibleItems() as $item) {
            $customModel = $this->orderDataFactory->create();

            $customModel->setData([
                OrderDataInterface::ORDER_ID => $order->getId(),
                OrderDataInterface::PRODUCT_ID => $item->getProductId(),
                OrderDataInterface::SKU => $item->getSku(),
                OrderDataInterface::PRICE => $item->getPrice(),
                OrderDataInterface::QTY_ORDERED => $item->getQtyOrdered(),
                OrderDataInterface::CREATED_AT => $order->getCreatedAt()
            ]);

            try {
                $this->orderDataRepository->save($customModel);
            } catch (\Exception $e) {
                // Implement logging logic here if necessary
            }
        }
    }
}
