<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model\Bonus\Types;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perspective\Bonuses\Model\Bonus\AbstractBonus;

/**
 * Gift Types.
 */
class Gift extends AbstractBonus
{
    public const string BONUS_CODE = 'gift';
    public const string MESSAGE_TEMPLATE = 'Bonus: gift - %s';

    /**
     * {@inheritdoc}
     */
    public function isApplicable($quote, $total): bool
    {
        if ($this->bonusValidator->isCartRulesApplied($quote)) { //якщо винести до менеджера то не спрацює логіка rollback
            return false;
        }

        if (!$this->isEnabled()){
            return false;
        }

        $config = $this->getConfig();
        $threshold = $config['threshold_min_total'];
        $giftId = $config['gift_sku'];
        if ($threshold == 0 && $giftId == 0) {
            return false;
        }

        $baseTotal = $total->getBaseGrandTotal();
        if ($baseTotal < $threshold) {
            return false;
        }

        if (!$this->bonusValidator->isProductSalable($this->config->getProductSkuById($giftId))) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function apply($quote, $total): array
    {
        $config = $this->getConfig();
        $gift = $this->config->getProductById($config['gift_sku']);

        $this->giftManager->addGiftToQuote($quote, $gift);

        $giftName = $gift->getName();
        $frontendMessages[] = sprintf(self::MESSAGE_TEMPLATE, $giftName);

        return [
            'bonus_discount' => 0,
            'bonus_messages' => $frontendMessages
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rollback($quote): void
    {
        $this->giftManager->removeGiftFromQuote($quote);
    }
}
