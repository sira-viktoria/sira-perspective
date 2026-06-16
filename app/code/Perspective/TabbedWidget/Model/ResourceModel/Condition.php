<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Condition Class.
 */
class Condition extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct(): void
    {
        $this->_init('perspective_widget_conditions', 'condition_id');
    }
}
