<?php
declare(strict_types=1);

namespace Perspective\CheckoutPlaceOrderService\Plugin\Model\Magewire\Payment;

use Hyva\Checkout\Model\Magewire\Payment\PlaceOrderServiceProcessor;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magewirephp\Magewire\Component;

/**
 * MaxItemsPlaceOrderValidation plugin.
 */
class MaxItemsPlaceOrderValidation
{
    /**
     * @var SessionCheckout
     */
    protected SessionCheckout $sessionCheckout;

    /**
     * @param SessionCheckout $sessionCheckout
     */
    public function __construct(
        SessionCheckout $sessionCheckout
    ) {
        $this->sessionCheckout = $sessionCheckout;
    }

    /**
     *
     * @throws LocalizedException
     */
    public function beforeProcess(
        PlaceOrderServiceProcessor $subject,
        Component $component,
        ?CartInterface $quote = null,
        array $data = []
    ): array {
        if ($quote === null) {
            $quote = $this->sessionCheckout->getQuote();
        }

        if ($quote instanceof CartInterface && (float)$quote->getItemsQty() > 20) {
            $errorText = __('Orders with more than 20 items require manager approval.');
            $quote->setHasError(true);
            $quote->addMessage($errorText);
        }

        return [$component, $quote, $data];
    }
}
