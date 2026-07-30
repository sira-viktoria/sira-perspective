<?php

declare(strict_types=1);

namespace Perspective\CheckoutExtensionAttributes\Model;

use Magento\Framework\Exception\AlreadyExistsException;
use Perspective\CheckoutExtensionAttributes\Api\ReferenceStorageInterface;
use Perspective\CheckoutExtensionAttributes\Model\ResourceModel\CustomerReference;

readonly class ReferenceStorage implements ReferenceStorageInterface
{
    public function __construct(
        private CustomerReference        $resource,
        private CustomerReferenceFactory $customerReferenceFactory
    ) {
    }

    /**
     * @throws AlreadyExistsException
     */
    public function save(
        int $quoteId,
        string $reference
    ): void {
        $model = $this->customerReferenceFactory->create();
        $this->resource->load($model, $quoteId, 'quote_id' );
        if (!$model->getId()) {
            $model = $this->customerReferenceFactory->create();
            $model->setQuoteId($quoteId);
        }

        $model->setCustomerReference($reference);
        $this->resource->save($model);
    }

    public function get(
        int $quoteId
    ): ?string {
        $model = $this->customerReferenceFactory->create();
        $this->resource->load($model,$quoteId, 'quote_id' );

        return $model->getCustomerReference();
    }
}
