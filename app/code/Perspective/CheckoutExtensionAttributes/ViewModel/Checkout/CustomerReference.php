<?php
declare(strict_types=1);

namespace Perspective\CheckoutExtensionAttributes\ViewModel\Checkout;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\CheckoutExtensionAttributes\Api\ReferenceStorageInterface;

readonly class CustomerReference implements ArgumentInterface
{
    /**
     * @param ReferenceStorageInterface $storage
     */
    public function __construct(
        private ReferenceStorageInterface $storage
    ) {
    }

    /**
     * @param $quoteId
     * @return string|null
     */
    public function getCustomerReference($quoteId): ?string
    {
        return $this->storage->get((int) $quoteId);
    }
}
