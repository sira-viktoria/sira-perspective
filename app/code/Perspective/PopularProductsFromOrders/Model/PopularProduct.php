<?php
declare(strict_types=1);

namespace Perspective\PopularProductsFromOrders\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Perspective\PopularProductsFromOrders\Model\ResourceModel\PopularProduct as PopularProductResourceModel;

/**
 * PopularProduct Model.
 */
class PopularProduct extends AbstractModel
{
    /**
     * Initialize resource model.
     *
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(PopularProductResourceModel::class);
    }
}
