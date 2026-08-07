<?php
declare(strict_types=1);

namespace Perspective\CustomPriceAttribute\Observer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * CustomPlpPrice Observer.
 */
class CustomPlpPrice implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var Collection $collection */
        $collection = $observer->getEvent()->getCollection();

        if (!$collection) {
            return;
        }

        $collection->addAttributeToSelect('custom_price');

        foreach ($collection as $product) {
            $customPrice = $product->getData('custom_price');

            if ($customPrice && $customPrice > 0) {
                $product->setPrice($customPrice);
            }
        }
    }
}
