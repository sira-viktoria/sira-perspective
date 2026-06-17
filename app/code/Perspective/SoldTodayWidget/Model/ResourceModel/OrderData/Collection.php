<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Model\ResourceModel\OrderData;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perspective\SoldTodayWidget\Model\OrderData;
use Perspective\SoldTodayWidget\Model\ResourceModel\OrderData as ResourceModelOrderData;

/**
 * Collection class.
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(OrderData::class, ResourceModelOrderData::class);
    }
}
