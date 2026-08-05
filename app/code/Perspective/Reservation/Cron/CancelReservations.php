<?php
declare(strict_types=1);

namespace Perspective\Reservation\Cron;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Perspective\Reservation\Api\ReservationEmailManagementInterface;
use Psr\Log\LoggerInterface;

/**
 * CancelReservations Class.
 */
class CancelReservations
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $orderCollectionFactory;

    /**
     * @var OrderManagementInterface
     */
    protected OrderManagementInterface $orderManagement;

    /**
     * @var ReservationEmailManagementInterface
     */
    protected ReservationEmailManagementInterface $emailManagement;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * CancelReservations constructor.
     *
     * @param CollectionFactory $orderCollectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param ReservationEmailManagementInterface $emailManagement
     */
    public function __construct(
        CollectionFactory $orderCollectionFactory,
        OrderManagementInterface $orderManagement,
        ReservationEmailManagementInterface $emailManagement,
        LoggerInterface $logger
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderManagement = $orderManagement;
        $this->emailManagement = $emailManagement;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $past24Hours = date('Y-m-d H:i:s', strtotime('-24 hours'));

        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('is_reservation', 1)
            ->addFieldToFilter('status', 'reservation')
            ->addFieldToFilter('created_at', ['lteq' => $past24Hours]);

        foreach ($orderCollection as $order) {
            try {
                $this->orderManagement->cancel($order->getId());

                $comment = __("The reservation was automatically canceled after 24 hours.");
                $order->addCommentToStatusHistory($comment, Order::STATE_CANCELED, false);
                $order->save();

                $this->emailManagement->sendCancellationEmail($order);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
