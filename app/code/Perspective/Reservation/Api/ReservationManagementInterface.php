<?php
declare(strict_types=1);

namespace Perspective\Reservation\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;

interface ReservationManagementInterface
{
    /**
     * @param array $data
     * @return OrderInterface
     * @throws LocalizedException
     */
    public function createReservationOrder(array $data): OrderInterface;
}
