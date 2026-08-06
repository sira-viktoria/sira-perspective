<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model\Totals;

use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Perspective\Bonuses\Model\Bonus\Manager;

/**
 * Bonus Class.
 */
class Bonus extends AbstractTotal
{
    /**
     * @var Manager
     */
    protected Manager $bonusManager;

    /**
     * Bonus constructor.
     *
     * @param Manager $bonusManager
     */
    public function __construct(
        Manager $bonusManager,
    ) {
        $this->bonusManager = $bonusManager;
        $this->setCode('bonus_total');
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ): static
    {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $frontendData = $this->bonusManager->applyBonuses($quote, $total);

        $total->setData('bonus_frontend_data', $frontendData);
        return $this;
    }

    /**
     * @param Total $total
     */

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total): array
    {
        $frontendData = $this->bonusManager->applyBonuses($quote, $total);
        if ($frontendData == null) {
            $frontendData = [
                'bonus_discount' => 0,
                'bonus_messages' => []
            ];
        }

        return [
            'code' => $this->getCode(),
            'title' => __(implode('||', $frontendData['bonus_messages'])),
            'value' => -$frontendData['bonus_discount'],
        ];
    }

    /**
     * @return Phrase
     */
    public function getLabel(): Phrase
    {
        return __('Bonus Total');
    }
}
