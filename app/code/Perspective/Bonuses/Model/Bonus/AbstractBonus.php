<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model\Bonus;

use Perspective\Bonuses\Model\BonusValidator;
use Perspective\Bonuses\Model\Config;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Perspective\Bonuses\Model\Bonus\Gift\GiftManager;

/**
 * AbstractBonus Class.
 */
abstract class AbstractBonus
{
    public const BONUS_CODE = '';

    public const MESSAGE_TEMPLATE = '';

    public const string BONUS_TOTAL_CODE = 'bonus_total';
    /**
     * @var BonusValidator
     */
    protected BonusValidator $bonusValidator;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var GiftManager
     */
    protected GiftManager $giftManager;

    /**
     * AbstractBonus constructor.
     *
     * @param BonusValidator $bonusValidator
     * @param Config $config
     * @param GiftManager $giftManager
     */
    public function __construct(
        BonusValidator $bonusValidator,
        Config $config,
        GiftManager $giftManager
    ) {
        $this->bonusValidator = $bonusValidator;
        $this->config = $config;
        $this->giftManager = $giftManager;
    }

    /**
     * Check if bonus can be applied to the cart
     *
     * @param CartInterface $quote
     * @param Total $total
     * @return bool
     */
    abstract public function isApplicable(CartInterface $quote, Total $total): bool;

    /**
     * Applies the bonus to the cart
     *
     * @param CartInterface $quote
     * @param Total $total
     * @return array  Result data for frontend (bonus amount, messages)
     */
    abstract public function apply(CartInterface $quote, Total $total): array;

    /**
     * Rolls back the bonus effects in the cart.
     *
     * @param CartInterface $quote
     * @return void
     */
    public function rollback(CartInterface $quote): void
    {
        //If you need custom rollback logic(example: gift bonus)
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return static::BONUS_CODE;
    }

    /**
     * @return mixed
     */
    public function getConfig(): mixed
    {
        return $this->bonusValidator->getBonusConfig($this->getCode());
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->bonusValidator->isBonusEnabled($this->getCode());
    }
}
