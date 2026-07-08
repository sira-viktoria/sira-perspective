<?php
declare(strict_types=1);

namespace Perspective\CheckoutDeliveryComment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * AddDeliveryCommentToHistory Observer.
 */
class AddDeliveryCommentToHistory implements ObserverInterface
{
    /**
     * @var OrderStatusHistoryRepositoryInterface
     */
    protected OrderStatusHistoryRepositoryInterface $orderStatusRepository;

    /**
     * @var OrderStatusHistoryInterfaceFactory
     */
    protected OrderStatusHistoryInterfaceFactory $orderStatusHistoryInterfaceFactory;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param OrderStatusHistoryRepositoryInterface $orderStatusRepository
     * @param OrderStatusHistoryInterfaceFactory $orderStatusHistoryInterfaceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderStatusHistoryRepositoryInterface $orderStatusRepository,
        OrderStatusHistoryInterfaceFactory $orderStatusHistoryInterfaceFactory,
        LoggerInterface $logger
    ) {
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStatusHistoryInterfaceFactory = $orderStatusHistoryInterfaceFactory;
        $this->logger = $logger;
    }

    /**
     * Save the customer comment as an order status history item when available.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (! $observer->hasData('order') || ! $observer->hasData('quote')) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $observer->getOrder();
        /** @var CartInterface $quote */
        $quote = $observer->getQuote();
        /** @var string|null $comment */
        $comment = $quote->getOrderDeliveryComment();

        if ($comment) {
            $statusHistoryItem = $this->orderStatusHistoryInterfaceFactory->create();

            $statusHistoryItem->setParentId($order->getId());
            $statusHistoryItem->setComment($comment);
            $statusHistoryItem->setIsVisibleOnFront(true);
            $statusHistoryItem->setIsCustomerNotified(false);
            $statusHistoryItem->setStatus($order->getStatus());

            try {
                $this->orderStatusRepository->save($statusHistoryItem);
            } catch (CouldNotSaveException $exception) {
                $this->logger->critical(
                    sprintf('Order comment for quote id "%s" could not be saved', $quote->getId()),
                    ['exception' => $exception]
                );
            }
        }
    }
}
