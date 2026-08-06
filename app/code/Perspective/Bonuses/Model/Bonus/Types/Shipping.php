<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model\Bonus\Types;

use Perspective\Bonuses\Model\Bonus\AbstractBonus;

/**
 * Shipping Types.
 */
class Shipping extends AbstractBonus
{
    public const string BONUS_CODE = "discount_shipping";
    public const string MESSAGE_TEMPLATE = 'Bonus: %d%% discount for shipping';

    /**
     * {@inheritdoc}
     */
    public function isApplicable($quote, $total): bool
    {
        if ($this->bonusValidator->isCartRulesApplied($quote)) {
            return false;
        }

        if (!$this->isEnabled()){
            return false;
        }

        $config = $this->getConfig();
        $firstThreshold = $config['first_threshold_min_total'];
        $firstDiscount  = $config['first_threshold_discount_value'];
        $secondThreshold = $config['second_threshold_min_total'];
        $secondDiscount  = $config['second_threshold_discount_value'];
        if ($firstThreshold == 0 && $firstDiscount == 0 && $secondThreshold == 0 && $secondDiscount == 0) {
            return false;
        }

        $baseTotal = $total->getData('base_subtotal');
        if ($baseTotal < min($firstThreshold, $secondThreshold)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($quote, $total): array
    {
        $config = $this->getConfig();
        $firstThreshold = $config['first_threshold_min_total'];
        $firstDiscount  = $config['first_threshold_discount_value'];
        $secondThreshold = $config['second_threshold_min_total'];
        $secondDiscount  = $config['second_threshold_discount_value'];
        $baseTotal = $total->getBaseGrandTotal();

        if ($baseTotal < max($firstThreshold, $secondThreshold)) {
            $discount = min($firstDiscount, $secondDiscount);
        } else {
            $discount = max($firstDiscount, $secondDiscount);
        }

        $baseShippingAmount = $total->getBaseShippingAmount();
        $discountAmount = $baseShippingAmount * ($discount / 100);

        $total->addTotalAmount(self::BONUS_TOTAL_CODE, -$discountAmount);
        $total->addBaseTotalAmount(self::BONUS_TOTAL_CODE, -$discountAmount);

        $frontendMessages[] = sprintf(self::MESSAGE_TEMPLATE, $discount);

        return [
            'bonus_discount' => $discountAmount,
            'bonus_messages' => $frontendMessages
        ];
    }
}
