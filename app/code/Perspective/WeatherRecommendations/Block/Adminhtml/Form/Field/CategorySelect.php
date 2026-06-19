<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Block\Adminhtml\Form\Field;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

/**
 * CategorySelect Class for rendering category list.
 */
class CategorySelect extends Select
{
    /** @var CollectionFactory */
    protected CollectionFactory $categoryCollectionFactory;

    /**
     * CategorySelect constructor.
     *
     * @param Context $context
     * @param CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value): mixed
    {
        return $this->setName($value . '[]');
    }

    /**
     * @param $value
     * @return CategorySelect
     */
    public function setInputId($value): CategorySelect
    {
        return $this->setId($value);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getCategoryTreeOptions());
        }

        return parent::_toHtml();
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    private function getCategoryTreeOptions(): array
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'level', 'parent_id'])
            ->addAttributeToFilter('is_active', 1)
            ->setStoreId(0)
            ->addAttributeToSort('path', 'asc');

        $categoryNamesMap = [];
        $options = [];
        foreach ($collection as $category) {
            $categoryNamesMap[$category->getId()] = $category->getName();
        }
        foreach ($collection as $category) {
            $level = (int)$category->getLevel();

            if ($level <= 1) {
                continue;
            }

            $indent = str_repeat('. ', ($level - 2) * 2);
            $parentId = $category->getParentId();
            $parentPrefix = '';

            if ($level > 2 && isset($categoryNamesMap[$parentId])) {
                $parentPrefix = '[' . $categoryNamesMap[$parentId] . '] ';
            }

            $options[] = [
                'label' => $indent . $parentPrefix . $category->getName() . ' (ID: ' . $category->getId() . ')',
                'value' => $category->getId()
            ];
        }

        return $options;
    }
}
