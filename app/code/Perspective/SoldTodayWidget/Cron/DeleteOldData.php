<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Perspective\SoldTodayWidget\Api\OrderDataRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * DeleteOldData Cron
 */
class DeleteOldData
{
    /**
     * @var OrderDataRepositoryInterface
     */
    protected OrderDataRepositoryInterface $repository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * DeleteOldData constructor.
     *
     * @param OrderDataRepositoryInterface $repository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderDataRepositoryInterface $repository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * @return $this
     */
    public function execute(): static
    {
        try {
            $thresholdDate = date('Y-m-d H:i:s', strtotime("-2 days"));

            $this->searchCriteriaBuilder->addFilter(
                'created_at',
                $thresholdDate,
                'lt'
            );

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults = $this->repository->getList($searchCriteria);
            $items = $searchResults->getItems();

            $deletedCount = 0;
            foreach ($items as $item) {
                $this->repository->delete($item);
                $deletedCount++;
            }

            $this->logger->info("Cron completed: Deleted $deletedCount old records from custom table.");
        } catch (\Exception $e) {
            $this->logger->error('Error deleting old data via repository: ' . $e->getMessage());
        }

        return $this;
    }
}
