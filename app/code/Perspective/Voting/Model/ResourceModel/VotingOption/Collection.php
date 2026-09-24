<?php
declare(strict_types=1);

namespace Perspective\Voting\Model\ResourceModel\VotingOption;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perspective\Voting\Model\ResourceModel\VotingOption as ResourceModel;
use Perspective\Voting\Model\VotingOption as Model;

/**
 * Collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'perspective_voting_option_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct(): void
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
