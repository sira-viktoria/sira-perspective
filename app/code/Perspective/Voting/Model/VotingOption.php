<?php
declare(strict_types=1);

namespace Perspective\Voting\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Perspective\Voting\Model\ResourceModel\VotingOption as ResourceModel;

/**
 * VotingOption Model.
 */
class VotingOption extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'perspective_voting_option_model';

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
