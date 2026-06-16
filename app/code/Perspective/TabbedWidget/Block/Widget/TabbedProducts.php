<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Block\Widget;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogRule\Model\Rule as CatalogRule;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Widget\Helper\Conditions;

/**
 * TabbedProducts Class.
 */
class TabbedProducts extends ProductsList
{
    /**
     * @var CatalogRule
     */
    protected CatalogRule $catalogRule;

    /**
     * TabbedProducts constructor.
     *
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param Builder $sqlBuilder
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param CatalogRule $catalogRule
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        CatalogRule $catalogRule,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data
        );
        $this->catalogRule = $catalogRule;
    }

    /**
     * Override parent function to get current widget tab conditions
     *
     * @return Combine
     */
    protected function getConditions(): Combine
    {
        $tabId = $this->getData('current_tab_id');

        $conditions = json_decode($this->getData('tab_conditions_' . $tabId), true);

        $postData = ['conditions' => $conditions];
        $this->catalogRule->loadPost($postData);
        return $this->catalogRule->getConditions();
    }

    /**
     * Get HTML for product list of a tab
     *
     * @param string $tabId
     * @return string
     * @throws LocalizedException
     */
    public function getProductListHtml(string $tabId): string
    {
        $this->setData('current_tab_id', $tabId);

        $this->setProductCollection($this->createCollection());

        // set template of the original ProductList widget
        $this->setTemplate('Magento_CatalogWidget::product/widget/content/grid.phtml');

        return $this->toHtml();
    }

    /**
     * Override parent function to delay collection creation
     * and get tab ID first
     *
     * @return $this|ProductsList|TabbedProducts
     */
    protected function _beforeToHtml(): TabbedProducts|ProductsList|static
    {
        return $this;
    }

    /**
     * @param $tabId
     * @return Phrase
     */
    public function getTabTitle($tabId): Phrase
    {
        return __($this->getData('tab_title_' . $tabId));
    }

    /**
     * @return Phrase
     */
    public function getWidgetTitle(): Phrase
    {
        return __($this->getData('widget_title'));
    }
}

