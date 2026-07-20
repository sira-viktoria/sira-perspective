<?php
declare(strict_types=1);

namespace Perspective\CheckoutPlaceOrderService\Service;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * GrandTotalValidationService Class.
 */
class GrandTotalValidationService
{
    public const float MAX_ALLOWED_LIMIT = 500.00;

    /**
     * @var SessionCheckout
     */
    protected SessionCheckout $sessionCheckout;

    /**
     * GrandTotalValidationService constructor.
     *
     * @param SessionCheckout $sessionCheckout
     */
    public function __construct(
        SessionCheckout $sessionCheckout
    ) {
        $this->sessionCheckout = $sessionCheckout;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function isAvailableForOrder(): bool
    {
        $quote = $this->getCurrentQuote();
        $quote->collectTotals();
        return $quote->getGrandTotal() >= self::MAX_ALLOWED_LIMIT;
    }

    /**
     * @return float
     */
    public function getMaxLimit(): float
    {
        return self::MAX_ALLOWED_LIMIT;
    }

    /**
     * @return CartInterface|Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCurrentQuote(): CartInterface|Quote
    {
        return $this->sessionCheckout->getQuote();
    }
}
