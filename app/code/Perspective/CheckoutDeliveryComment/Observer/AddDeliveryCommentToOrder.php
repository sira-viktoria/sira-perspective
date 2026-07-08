<?php
declare(strict_types=1);

namespace Perspective\CheckoutDeliveryComment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

/**
 * AddDeliveryCommentToOrder Observer.
 */
class AddDeliveryCommentToOrder implements ObserverInterface
{
    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {

        /** @var Quote $quote */
        $quote = $observer->getData('quote');
        /** @var Order $order */
        $order = $observer->getData('order');

        $comment = $quote->getOrderDeliveryComment();
        if ($comment) {
            $order->setData('order_delivery_comment', $comment);
        }
    }
}
