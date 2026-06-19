<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

/**
 * DynamicConditionsGrid Class.
 */
class DynamicConditionsGrid extends AbstractFieldArray
{
    /**
     * @var CategorySelect
     */
    private $categoryRenderer;

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('weather', [
            'label'   => __('Weather'),
            'style' => 'width:150px'

        ]);

        $this->addColumn('temperature_from', [
            'label'   => __('Temp. From'),
            'class' => 'validate-number',
            'style' => 'width:100px',
        ]);

        $this->addColumn('temperature_to', [
            'label'   => __('Temp. To'),
            'class' => 'validate-number',
            'style' => 'width:100px',
        ]);

        $this->addColumn('categories', [
            'label' => __('Categories'),
            'renderer' => $this->getCategoryRenderer(),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add a condition');;
    }

    /**
     * @return BlockInterface|CategorySelect
     * @throws LocalizedException
     */
    private function getCategoryRenderer(): CategorySelect|BlockInterface
    {
        if (!$this->categoryRenderer) {
            $this->categoryRenderer = $this->getLayout()->createBlock(
                CategorySelect::class,
                '',
                ['data' => ['is_render_to_js_template' => true, ]]
            );
        }
        $this->categoryRenderer->setExtraParams('style="width:250px !important;"');
        $this->categoryRenderer->setExtraParams('multiple="multiple" style="width:300px !important; height:200px;"');

        return $this->categoryRenderer;
    }

    /**
     * @param DataObject $row
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $categoryIds = $row->getData('category');

        if (is_array($categoryIds) && count($categoryIds) > 0) {
            foreach ($categoryIds as $id) {
                $hash = $this->getCategoryRenderer()->calcOptionHash($id);
                $options['option_' . $hash] = 'selected="selected"';
            }
        } elseif ($categoryIds !== null) {
            $hash = $this->getCategoryRenderer()->calcOptionHash($categoryIds);
            $options['option_' . $hash] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
