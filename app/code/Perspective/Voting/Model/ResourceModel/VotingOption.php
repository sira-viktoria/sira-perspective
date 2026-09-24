<?php
declare(strict_types=1);

namespace Perspective\Voting\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * VotingOption ResourceModel.
 */
class VotingOption extends AbstractDb
{
    /**
     * @var string
     */
    protected string $_eventPrefix = 'perspective_voting_option_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct(): void
    {
        $this->_init('pst_voting_option', 'option_id');
        $this->_useIsObjectNew = true;
    }
}
