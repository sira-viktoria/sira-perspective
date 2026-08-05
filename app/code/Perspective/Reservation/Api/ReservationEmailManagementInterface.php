<?php
declare(strict_types=1);

namespace Perspective\Reservation\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface ReservationEmailManagementInterface
{
    /*** @param OrderInterface $order
     * @param string $customerEmail
     * @param string $untilDate
     * @return void
     */
    public function sendReservationEmails(OrderInterface $order, string $customerEmail, string $untilDate): void;

    /**
     * @param OrderInterface $order
     * @return void
     */
    public function sendCancellationEmail(OrderInterface $order): void;
}
