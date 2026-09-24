<?php
declare(strict_types=1);

namespace Perspective\Voting\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Voting ResourceModel.
 */
class Voting extends AbstractDb
{
    /**
     * @var string
     */
    protected string $_eventPrefix = 'perspective_voting_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct(): void
    {
        $this->_init('pst_voting', 'voting_id');
        $this->_useIsObjectNew = true;
    }
}
