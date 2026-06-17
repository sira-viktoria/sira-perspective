<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perspective\SoldTodayWidget\Api\Data\OrderDataInterface;
use Perspective\SoldTodayWidget\Api\Data\OrderDataSearchResultInterface;
use Perspective\SoldTodayWidget\Api\OrderDataRepositoryInterface;
use Perspective\SoldTodayWidget\Model\OrderDataFactory as OrderDataFactory;
use Perspective\SoldTodayWidget\Model\ResourceModel\OrderData as OrderDataResource;
use Perspective\SoldTodayWidget\Model\ResourceModel\OrderData\CollectionFactory;
use Perspective\SoldTodayWidget\Api\Data\OrderDataSearchResultInterfaceFactory;
/**
 * OrderDataRepository Class.
 */
class OrderDataRepository implements OrderDataRepositoryInterface
{
    /**
     * @var OrderDataResource
     */
    protected OrderDataResource $resource;

    /**
     * @var OrderDataFactory
     */
    protected OrderDataFactory $factory;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
 * @var CollectionProcessorInterface
 */
    protected CollectionProcessorInterface $collectionProcessor;

    /**
     * @var OrderDataSearchResultInterfaceFactory
     */
    protected OrderDataSearchResultInterfaceFactory $searchResultsFactory;

    /**
     * @param OrderDataResource $resource
     * @param OrderDataFactory $factory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        OrderDataResource $resource,
        OrderDataFactory $factory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        OrderDataSearchResultInterfaceFactory $searchResultsFactory
    ){
        $this->resource = $resource;
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * OrderDataRepository constructor.
     *
     * @param OrderDataInterface $orderData
     * @return OrderDataInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderDataInterface $orderData): OrderDataInterface
    {
        try {
            $this->resource->save($orderData);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $orderData;
    }

    /**
     * @param $id
     * @return OrderDataInterface
     * @throws NoSuchEntityException
     */
    public function getById($id): OrderDataInterface
    {
        $orderData = $this->factory->create();
        $this->resource->load($orderData, $id);
        if (!$orderData->getId()) {
            throw new NoSuchEntityException(__('Order data with ID "%1" does not exist.', $id));
        }
        return $orderData;
    }

    /**
     * @param OrderDataInterface $orderData
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(OrderDataInterface $orderData): bool
    {
        try {
            $this->resource->delete($orderData);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param $id
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($id): bool
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
