<?php

declare(strict_types=1);

namespace Perspective\CheckoutExtensionAttributes\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerReference extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            'perspective_customer_reference',
            'entity_id'
        );
    }
}
