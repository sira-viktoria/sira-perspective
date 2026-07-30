<?php

declare(strict_types=1);

namespace Perspective\CheckoutExtensionAttributes\Magewire\Checkout;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magewirephp\Magewire\Component;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Perspective\CheckoutExtensionAttributes\Api\ReferenceStorageInterface;

class CustomerReference extends Component
{
    public ?string $customerReference = null;

    public bool $saved = false;

    /**
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param ReferenceStorageInterface $storage
     */
    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly ReferenceStorageInterface $storage
    ) {
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function mount(): void
    {
        $quote = $this->checkoutSession->getQuote();
        $customerReference = $quote->getExtensionAttributes()->getCustomerReference();

        if ($customerReference === null && $quote->getId()) {
            $customerReference = $this->storage->get((int) $quote->getId());
        }

        $this->customerReference = $customerReference;
    }

    /**
     * @param string $value
     * @return string
     */
    public function updatingCustomerReference(string $value): string
    {
        try {
            $quote = $this->checkoutSession->getQuote();
            $quote->getExtensionAttributes()->setCustomerReference($value);

            $this->quoteRepository->save($quote);

            if ($quote->getId()) {
                $this->storage->save(
                    (int) $quote->getId(),
                    $value
                );
            }
            $this->saved = true;
        } catch (LocalizedException| Exception $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving your comment. Please try again.');
        }

        return $value;
    }
}
