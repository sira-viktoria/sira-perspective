<?php
declare(strict_types=1);

namespace Perspective\Voting\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * ManagementType Source Class.
 */
class ManagementType implements OptionSourceInterface
{
    public const int TYPE_MANUAL = 0;
    public const int TYPE_BY_DATE = 1;

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::TYPE_MANUAL, 'label' => __('Manual')],
            ['value' => self::TYPE_BY_DATE, 'label' => __('By Date')]
        ];
    }
}
