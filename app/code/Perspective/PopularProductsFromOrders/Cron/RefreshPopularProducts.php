<?php
declare(strict_types=1);
namespace Perspective\PopularProductsFromOrders\Cron;

use Magento\Framework\App\Cache\Frontend\Pool;
use Perspective\PopularProductsFromOrders\Service\Config;
use Perspective\PopularProductsFromOrders\Service\GetPopularProducts;
use Psr\Log\LoggerInterface;
use Throwable;
use Zend_Cache;

/***
 * RefreshPopularProducts Cron Job.
 */
class RefreshPopularProducts
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var GetPopularProducts
     */
    protected GetPopularProducts $popularProductsManager;
    /**
     * @var Pool
     */
    protected Pool $frontendCachePool;
    /**
     * @var Config
     */
    protected Config $configDataService;

    /**
     * @param LoggerInterface $logger
     * @param GetPopularProducts $popularProductsManager
     * @param Pool $frontendCachePool
     * @param Config $configDataService
     */
    public function __construct(
        LoggerInterface $logger,
        GetPopularProducts $popularProductsManager,
        Pool $frontendCachePool,
        Config $configDataService,
    ) {
        $this->logger = $logger;
        $this->popularProductsManager = $popularProductsManager;
        $this->frontendCachePool = $frontendCachePool;
        $this->configDataService = $configDataService;
    }

    /**
     * Update popular products and clean cache for pages where they are shown
     *
     * @return void
     */
    public function execute(): void
    {
        if (!$this->configDataService->isEnabled()) {
            return;
        }

        try {
            $this->popularProductsManager->refreshTopProducts();
            $this->logger->notice(__('Popular products top successfully updated.'));
        } catch (Throwable $e) {
            $this->logger->error(__('Error refreshing popular products: %1', $e->getMessage()));
            return;
        }

        foreach ($this->frontendCachePool as $frontendCache) {
            try {
                $frontendCache->clean(
                    Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                    ['PERSPECTIVE_POPULAR_PRODUCTS']
                );
            } catch (Throwable $e) {
                $this->logger->error(__('Error cleaning FPC: %1', $e->getMessage()));
            }
        }
        $this->logger->notice(__('FPC cleaned on pages with popular-product-slider'));
    }
}
