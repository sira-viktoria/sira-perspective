<?php
declare(strict_types=1);

namespace Perspective\Memes\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Perspective\Memes\Model\Memes\MemeManager;

/**
 * CopyMemeDataToOrder Observer.
 */
class CopyMemeDataToOrder implements ObserverInterface
{
    /**
     * @var MemeManager
     */
    protected MemeManager $memeManager;

    /**
     * CopyMemeDataToOrder constructor.
     *
     * @param MemeManager $memeManager
     */
    public function __construct(
        MemeManager $memeManager
    ) {
        $this->memeManager = $memeManager;
    }

    /**
     * Copy memes data from quote to order when order is placed
     *
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = (int)$order->getQuoteId();

        $memesData = $this->memeManager->getData((int)$quoteId, 'quote');
        $order->setData('order_memes', json_encode($memesData));
    }
}
