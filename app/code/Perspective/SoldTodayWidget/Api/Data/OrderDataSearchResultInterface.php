<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * OrderDataSearchResultInterface
 */
interface OrderDataSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get items list
     *
     * @return OrderDataInterface[]
     */
    public function getItems(): array;

    /**
     * Set items list
     *
     * @param OrderDataInterface[] $items
     */
    public function setItems(array $items);
}
