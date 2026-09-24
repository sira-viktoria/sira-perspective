<?php
declare(strict_types=1);

namespace Perspective\Voting\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * VotingVote ResourceModel.
 */
class VotingVote extends AbstractDb
{
    /**
     * @var string
     */
    protected string $_eventPrefix = 'perspective_voting_vote_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct(): void
    {
        $this->_init('pst_voting_vote', 'vote_id');
        $this->_useIsObjectNew = true;
    }
}
