<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Model;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Perspective\TabbedWidget\Model\ResourceModel\Condition\CollectionFactory;

/**
 * DataProvider Class.
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider Class.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $conditionCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $conditionCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $conditionCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return null|array
     */
    public function getData(): ?array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        foreach ($items as $condition) {
            $this->loadedData[$condition->getConditionId()] = $condition->getData();
        }
        return $this->loadedData;
    }
}
