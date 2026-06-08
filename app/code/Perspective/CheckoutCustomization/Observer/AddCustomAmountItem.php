<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CheckoutCustomization\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * AddCustomAmountItem Observer.
 */
class AddCustomAmountItem implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $customAmount = 100;
        $cart->addCustomItem(__('Custom Amount'), 1, -1.00 * $customAmount, 'customfee');
    }
}
