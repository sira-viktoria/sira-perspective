<?php

declare(strict_types=1);

namespace Perspective\CheckoutExtensionAttributes\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Perspective\CheckoutExtensionAttributes\Model\ResourceModel\CustomerReference as ResourceModel;

class CustomerReference extends AbstractModel
{
    /**
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function getQuoteId(): int
    {
        return (int)$this->getData('quote_id');
    }

    public function setQuoteId(int $id): self
    {
        return $this->setData('quote_id', $id);
    }

    public function getCustomerReference(): ?string
    {
        return $this->getData('customer_reference');
    }

    public function setCustomerReference(?string $reference): self
    {
        return $this->setData('customer_reference', $reference);
    }
}
