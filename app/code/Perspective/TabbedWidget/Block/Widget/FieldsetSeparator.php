<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Block\Widget;

use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Widget\Block\BlockInterface;

/**
 * Custom Fieldset Separator.
 */
class FieldsetSeparator extends Fieldset implements BlockInterface
{
    /**
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $label = $element->getLabel();
        $fieldsetHtml = '
         <fieldset class="fieldset admin__fieldset" id="fieldset_' . $element->getHtmlId() . '" style="margin-bottom: -25px; margin-top: -25px; width: 100%;">
                <legend class="admin__legend legend">
                              <h1 style="solid; color:black;">' . __($label) . ' </h1>
                </legend>
         </fieldset>';
        $element->setData('after_element_html', $fieldsetHtml);

        return $element;
    }
}
