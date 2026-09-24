<?php
declare(strict_types=1);

namespace Perspective\Voting\Model\ResourceModel\Voting;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perspective\Voting\Model\ResourceModel\Voting as ResourceModel;
use Perspective\Voting\Model\Voting as Model;

/**
 * Collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'perspective_voting_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct(): void
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
