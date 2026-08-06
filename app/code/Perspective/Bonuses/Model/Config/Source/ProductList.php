<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * ProductList Class.
 */
class ProductList implements \Magento\Framework\Option\ArrayInterface
{
    protected CollectionFactory $collectionFactory;

    /**
     * ProductList constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('id')
            ->addAttributeToFilter('type_id', 'simple')
            ->setOrder('name', 'ASC');

        $options = [];
        foreach ($collection as $product) {
            $options[] = [
                'value' => $product->getId(),
                'label' => $product->getName()
            ];
        }

        return $options;
    }
}
