<?php
declare(strict_types=1);

namespace Perspective\Voting\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Perspective\Voting\Model\ResourceModel\VotingVote as ResourceModel;

/**
 * VotingVote Model.
 */
class VotingVote extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'perspective_voting_vote_model';

    /**
     * Initialize magento model.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }
}
