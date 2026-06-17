<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perspective\SoldTodayWidget\Api\Data\OrderDataInterface;
use Perspective\SoldTodayWidget\Api\Data\OrderDataSearchResultInterface;

/**
 * Repository interface.
 */
interface OrderDataRepositoryInterface
{
    /**
     * @param OrderDataInterface $orderData
     * @return OrderDataInterface
     * @throws LocalizedException
     */
    public function save(OrderDataInterface $orderData): OrderDataInterface;

    /**
     * @param int $id
     * @return OrderDataInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): OrderDataInterface;

    /**
     * @param OrderDataInterface $orderData
     * @return bool
     * @throws LocalizedException
     */
    public function delete(OrderDataInterface $orderData): bool;

    /**
     * @param int $id
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $id): bool;

    /**
     * Get list of custom data items matching search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
