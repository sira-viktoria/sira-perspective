<?php

declare(strict_types=1);

namespace Perspective\CheckoutExtensionAttributes\Model\ResourceModel\CustomerReference;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perspective\CheckoutExtensionAttributes\Model\CustomerReference as Model;
use Perspective\CheckoutExtensionAttributes\Model\ResourceModel\CustomerReference as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            Model::class,
            ResourceModel::class
        );
    }
}
