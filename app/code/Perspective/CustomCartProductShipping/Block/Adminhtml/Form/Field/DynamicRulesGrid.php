<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomCartProductShipping\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * DynamicRulesGrid Class.
 */
class DynamicRulesGrid extends AbstractFieldArray
{
    /**
     * @return void
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('qty', [
            'label' => __('Number of products'),
            'class' => 'validate-number validate-greater-than-zero',
            'style' => 'width:150px'
        ]);
        $this->addColumn('discount', [
            'label' => __('Discount (%)'),
            'class' => 'validate-number validate-zero-or-greater',
            'style' => 'width:150px'
        ]);

        $this->_addButtonLabel = __('Add a rule');
    }
}
