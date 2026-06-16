<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Block\Adminhtml\Condition\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as RuleConditions;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Made to make the catalog conditions tree more universal
 * Original file: vendor/magento/module-catalog-rule/Block/Adminhtml/Promo/Catalog/Edit/Tab/Conditions.php
 * (the original catalog rule version is not as universal as the sales rules version)
 *
 * Based on:
 * vendor/magento/module-sales-rule/Block/Adminhtml/Promo/Quote/Edit/Tab/Conditions.php
 */
class ConditionsTree extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    protected Fieldset $rendererFieldset;

    /**
     * @var RuleConditions
     */
    protected RuleConditions $conditions;

    /**
     * @var RuleFactory
     */
    protected RuleFactory $ruleFactory;

    /**
     * ConditionsTree constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RuleConditions $conditions
     * @param Fieldset $rendererFieldset
     * @param RuleFactory $ruleFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleConditions $conditions,
        Fieldset $rendererFieldset,
        RuleFactory $ruleFactory,
        array $data = []
    ) {
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
        $this->ruleFactory = $ruleFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Phrase
     */
    public function getTabLabel(): Phrase
    {
        return __('Conditions');
    }

    /**
     * @return Phrase
     */
    public function getTabTitle(): Phrase
    {
        return __('Conditions');
    }

    /**
     * @return bool
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return false;
    }

    /**
     * @return null
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isAjaxLoaded(): bool
    {
        return false;
    }

    /**
     * Prepare form with conditions fieldset
     *
     * @return ConditionsTree
     * @throws LocalizedException
     */
    protected function _prepareForm(): ConditionsTree
    {
        $model = $this->_coreRegistry->registry('current_promo_catalog_rule');

        if (!$model) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->ruleFactory->create();
            if ($id) {
                $model->load($id);
            }
        }

        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add conditions fieldset to form
     *
     * @param $model
     * @param string $fieldsetId
     * @param string $formName
     * @return Form
     * @throws LocalizedException
     */
    protected function addTabToForm(
        $model,
        string $fieldsetId = 'conditions_fieldset',
        string $formName = 'perspective_widget_conditions_form'
    ): Form {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'catalog_rule/promo_catalog/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $renderer = $this->getLayout()->createBlock(Fieldset::class);
        $renderer->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId);

        $fieldset = $form->addFieldset(
            $fieldsetId,
            ['legend' => __('Conditions (don\'t add conditions if rule is applied to all products)')]
        )->setRenderer($renderer);

        $fieldset->addField(
            'conditions_display',
            'text',
            [
                'name' => 'conditions_display',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )
            ->setRule($model)
            ->setRenderer($this->conditions);

        $fieldset->addField(
            'conditions',
            'hidden',
            [
                'name' => 'conditions',
                'data-form-part' => $formName,
                'value' => $model->getConditionsSerialized(),
            ]
        );

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName, $conditionsFieldSetId);
        return $form;
    }

    /**
     * @param AbstractCondition $conditions
     * @param $formName
     * @param $jsFormName
     * @return void
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName, $jsFormName): void
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
    }
}
