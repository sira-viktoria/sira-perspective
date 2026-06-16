<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Perspective\TabbedWidget\Model\ResourceModel\Condition\CollectionFactory;

/**
 * ConditionList Class.
 */
class ConditionList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * ConditionList constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create();
        $options = [];
        foreach ($collection as $item) {
            $options[] = [
                'label' => $item->getName(),
                'value' => $item->getConditions(),
            ];
        }
        return $options;
    }
}
