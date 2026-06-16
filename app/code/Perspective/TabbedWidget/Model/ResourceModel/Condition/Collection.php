<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Model\ResourceModel\Condition;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perspective\TabbedWidget\Model\ResourceModel\Condition as ConditionResourceModel;
use Perspective\TabbedWidget\Model\Condition as ConditionModel;

/**
 * Collection Class.
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ConditionModel::class, ConditionResourceModel::class);
    }
}
