<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Model\Service;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * UpdateOrderStatus Class.
 */
class UpdateOrderStatus
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var Order
     */
    protected Order $order;

    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * UpdateOrderStatus constructor.
     *
     * @param Order $order
     * @param LoggerInterface $logger
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Order $order,
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->order = $order;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param $orderId
     * @param $status
     * @return void
     */
    public function execute($orderId, $status): void
    {
        try {
            $order = $this->order->loadByIncrementId($orderId);
            if ($order->getId()) {
                $order->setState($status)->setStatus($status);
                $this->orderRepository->save($order);
                $this->logger->info('Order status updated successfully.', ['order_id' => $orderId, 'status' => $status]);
            } else {
                $this->logger->error('Order not found.', ['order_id' => $orderId]);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error updating order status.', ['error' => $e->getMessage()]);
        }
    }
}
