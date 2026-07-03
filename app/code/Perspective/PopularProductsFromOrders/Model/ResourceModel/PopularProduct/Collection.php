<?php
declare(strict_types=1);

namespace Perspective\PopularProductsFromOrders\Model\ResourceModel\PopularProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perspective\PopularProductsFromOrders\Model\ResourceModel\PopularProduct as PopularProductResourceModel;
use Perspective\PopularProductsFromOrders\Model\PopularProduct as PopularProductModel;

/**
 * Collection Class.
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(PopularProductModel::class, PopularProductResourceModel::class);
    }
}
