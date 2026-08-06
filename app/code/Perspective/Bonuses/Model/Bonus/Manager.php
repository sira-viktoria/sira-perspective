<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model\Bonus;

use Magento\Quote\Api\Data\CartInterface;
use Perspective\Bonuses\Model\BonusValidator;
use Magento\Quote\Model\Quote\Address\Total;

/**
 * Manager Class.
 */
class Manager
{
    /**
     * @var BonusValidator
     */
    protected BonusValidator $bonusValidator;

    /** @var AbstractBonus[] */
    private array $bonuses;

    /**
     * Manager constructor.
     *
     * @param BonusValidator $bonusValidator
     * @param array $bonuses
     */
    public function __construct(
        BonusValidator $bonusValidator,
        array $bonuses = []
    ) {
        $this->bonusValidator = $bonusValidator;
        $this->bonuses = $bonuses;
    }

    /**
     *  Applies all applicable bonuses to the given cart quote and totals.
     *
     *  For each bonus:
     *  - Checks if it is applicable.
     *  - Applies it and collects bonus discount and messages.
     *  - Rolls back the bonus if not applicable.
     *
     * @param CartInterface $quote
     * @param Total $total
     * @return array
     */
    public function applyBonuses (CartInterface $quote, Total $total): array
    {
        $result = [
            'bonus_discount' => 0,
            'bonus_messages' => []
        ];

        $items = $quote->getItems();
        if (!$items) {
            return $result;
        }

        foreach ($this->bonuses as $bonus) {
            if ($bonus->isApplicable($quote, $total)) {
                $bonusResult = $bonus->apply($quote, $total);
                if (isset($bonusResult['bonus_discount'])) {
                    $result['bonus_discount'] += $bonusResult['bonus_discount'];
                    $result['bonus_messages'] = array_merge(
                        $result['bonus_messages'],
                        $bonusResult['bonus_messages']
                    );
                }
            } else {
                $bonus->rollback($quote);
            }
        }

        return $result;
    }
}
