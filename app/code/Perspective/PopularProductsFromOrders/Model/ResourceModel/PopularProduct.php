<?php
declare(strict_types=1);

namespace Perspective\PopularProductsFromOrders\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * PopularProduct Resource Model.
 */
class PopularProduct extends AbstractDb
{
    protected $_isPkAutoIncrement = false;

    /**
     * Initialize resource model.
     *
     * @return void
     */
    public function _construct(): void
    {
        $this->_init('perspective_popular_products', 'id');
    }
}
