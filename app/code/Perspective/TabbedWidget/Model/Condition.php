<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Model;

use Magento\Framework\Model\AbstractModel;
use Perspective\TabbedWidget\Model\ResourceModel\Condition as ConditionResourceModel;

/**
 * Condition Class.
 */
class Condition extends AbstractModel
{
    /**
     * Initialize resource model
     */
    protected function _construct(): void
    {
        $this->_init(ConditionResourceModel::class);
    }
}
