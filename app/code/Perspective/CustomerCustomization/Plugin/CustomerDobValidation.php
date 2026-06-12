<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomerCustomization\Plugin;

use Magento\Customer\Block\Widget\Dob;
use Magento\Framework\Escaper;

/**
 * CustomerDobValidation Plugin.
 */
class CustomerDobValidation
{
    /**
     * @var Escaper
     */
    protected Escaper $escaper;

    /**
     * CustomerDobValidation constructor.
     *
     * @param Escaper $escaper
     */
    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    /**
     * @param Dob $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundGetHtmlExtraParams(Dob $subject, callable $proceed): string
    {
        $validators = [];
        $validators['dob-age-validator'] = true;
        if ($subject->isRequired()) {
            $validators['required'] = true;
        }
        $validators['validate-date'] = [
            'dateFormat' => $subject->getDateFormat()
        ];
        $validators['validate-dob'] = [
            'dateFormat' => $subject->getDateFormat()
        ];

        return 'data-validate="' . $this->escaper->escapeHtml(json_encode($validators)) . '"';
    }
}
