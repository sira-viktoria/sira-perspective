<?php
declare(strict_types=1);

namespace Perspective\Voting\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Perspective\Voting\Model\ResourceModel\Voting as ResourceModel;

/**
 * Voting Model.
 */
class Voting extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'perspective_voting_model';

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
