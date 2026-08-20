<?php
declare(strict_types=1);

namespace Perspective\Memes\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Backend\Model\Session\Quote as AdminQuoteSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Perspective\Memes\Model\Memes\MemeManager;

/**
 * CopyMemesToAdminQuote Observer.
 */
class CopyMemesToAdminQuote implements ObserverInterface
{
    /**
     * @var MemeManager
     */
    protected MemeManager $memeManager;

    /**
     * @var AdminQuoteSession
     */
    protected AdminQuoteSession $adminQuoteSession;

    /**
     * CopyMemesToAdminQuote constructor.
     *
     * @param MemeManager $memeManager
     * @param AdminQuoteSession $adminQuoteSession
     */
    public function __construct(
        MemeManager $memeManager,
        AdminQuoteSession $adminQuoteSession
    ) {
        $this->memeManager = $memeManager;
        $this->adminQuoteSession = $adminQuoteSession;
    }

    /**
     * Copy memes from parent order to new quote in admin
     *
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$quote->getData('order_memes')) {
            $parentOrderId = (int)$this->adminQuoteSession->getOrderId();
            $parentMemesData = $this->memeManager->getData($parentOrderId, 'order');

            $quote->setData('order_memes', json_encode($parentMemesData));
        }
    }
}
