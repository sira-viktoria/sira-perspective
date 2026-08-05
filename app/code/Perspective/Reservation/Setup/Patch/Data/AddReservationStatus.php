<?php
declare(strict_types=1);

namespace Perspective\Reservation\Setup\Patch\Data;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;

/**
 * AddReservationStatus Patch.
 */
class AddReservationStatus implements DataPatchInterface
{

    /**
     * @var StatusFactory
     */
    protected StatusFactory $statusFactory;

    /**
     * @var StatusResource
     */
    protected StatusResource $statusResource;

    /**
     * AddReservationStatus constructor.
     *
     * @param StatusFactory $statusFactory
     * @param StatusResource $statusResource
     */
    public function __construct(
        StatusFactory $statusFactory, StatusResource $statusResource
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResource = $statusResource;
    }

    /**
     * @return $this|AddReservationStatus
     * @throws AlreadyExistsException
     * @throws \Exception
     */
    public function apply(): AddReservationStatus|static
    {
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => 'reservation',
            'label' => 'Reservation'
        ]);
        $this->statusResource->save($status);
        $status->assignState('new', false, true);
        return $this;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
