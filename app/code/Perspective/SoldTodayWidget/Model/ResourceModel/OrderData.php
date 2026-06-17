<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Perspective\SoldTodayWidget\Api\Data\OrderDataInterface;

/**
 * OrderData Class.
 */
class OrderData extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(OrderDataInterface::MAIN_TABLE, OrderDataInterface::ID);
    }
}
