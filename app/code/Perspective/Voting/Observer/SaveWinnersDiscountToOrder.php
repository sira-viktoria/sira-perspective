<?php
declare(strict_types=1);

namespace Perspective\Voting\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Perspective\Voting\Service\ActiveWinners;

/**
 * SaveWinnersDiscountToOrder Observer.
 */
class SaveWinnersDiscountToOrder implements ObserverInterface
{
    /**
     * @var ActiveWinners
     */
    protected ActiveWinners $activeWinnersService;

    /**
     *
     * SaveWinnersDiscountToOrder constructor.
     *
     * @param ActiveWinners $activeWinnersService
     */
    public function __construct(
        ActiveWinners $activeWinnersService
    ) {
        $this->activeWinnersService = $activeWinnersService;
    }

    /**
     * Get winners discount amount and save it to the order
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $discount = $this->activeWinnersService->getOrderWinnersDiscount($quote);
        $order->setData('winners_discount_amount', $discount);
    }
}
