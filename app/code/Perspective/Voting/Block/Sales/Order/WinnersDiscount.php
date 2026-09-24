<?php
declare(strict_types=1);

namespace Perspective\Voting\Block\Sales\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;
use Perspective\Voting\Service\ActiveWinners;

/**
 * WinnersDiscount Class.
 */
class WinnersDiscount extends Template
{
    /**
     * @var Order
     */
    protected Order $order;

    /**
     * @var DataObject
     */
    protected DataObject $source;

    /**
     * @var ActiveWinners
     */
    protected ActiveWinners $activeWinnersService;

    /**
     * WinnersDiscount constructor.
     *
     * @param Template\Context $context
     * @param ActiveWinners $activeWinnersService
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ActiveWinners $activeWinnersService,
        array $data = []
    ) {
        $this->activeWinnersService = $activeWinnersService;
        parent::__construct($context, $data);
    }

    /**
     * Add winners discount to order totals summary
     * (Used at admin/customer order view)
     *
     * @return $this
     */
    public function initTotals(): static
    {
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();

        $value = $this->order->getData('winners_discount_amount');
        if ($value != 0) {
            $this->source = $parent->getSource();
            $total = new DataObject(
                [
                    'code'=>'perspective_voting_winners_discount_total',
                    'strong'=>false,
                    'value'=>$value,
                    'label'=>__('Winners Discount'),
                ]
            );
            $parent->addTotal($total, 'perspective_voting_winners_discount_total');
        }
        return $this;
    }

    /**
     * @return true
     */
    public function displayFullSummary(): true
    {
        return true;
    }

    /**
     * @return DataObject
     */
    public function getSource(): DataObject
    {
        return $this->source;
    }

    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->order->getStore();
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return array
     */
    public function getLabelProperties(): array
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties(): array
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
