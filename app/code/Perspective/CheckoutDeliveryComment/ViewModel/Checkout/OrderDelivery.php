<?php
declare(strict_types=1);

namespace Perspective\CheckoutDeliveryComment\ViewModel\Checkout;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class OrderDelivery implements ArgumentInterface
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
     * @return string|null
     */
    public function getOrderComment(): ?string
    {
        try {
            return $this->sessionCheckout->getQuote()->getData('order_delivery_comment');
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return null;
        }
    }
}
