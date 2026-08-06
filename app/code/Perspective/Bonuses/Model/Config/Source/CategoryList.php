<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Option\ArrayInterface;

/**
 * CategoryList Class.
 */
class CategoryList implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $categoryCollectionFactory;

    /**
     * CategoryList constructor.
     *
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Return array of categories that contain at least one product
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addFieldToFilter('level', ['gt' => 1])
            ->addFieldToFilter('is_active', 1);

        $options = [];
        foreach ($collection as $category) {
            if ($category->getProductCount() > 0) {
                $options[] = [
                    'value' => $category->getId(),
                    'label' => $category->getName()
                ];
            }
        }

        return $options;
    }
}
