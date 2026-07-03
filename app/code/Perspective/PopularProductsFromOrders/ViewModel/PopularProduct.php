<?php
declare(strict_types=1);

namespace Perspective\PopularProductsFromOrders\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Perspective\PopularProductsFromOrders\Service\Config;
use Perspective\PopularProductsFromOrders\Service\GetPopularProducts;

/**
 * PopularProduct ViewModel.
 */
class PopularProduct implements ArgumentInterface, IdentityInterface
{
    /**
     * @var GetPopularProducts
     */
    protected GetPopularProducts $getPopularProducts;

    /**
     * @var Config
     */
    protected Config $configDataService;

    /**
     * PopularProduct constructor.
     *
     * @param GetPopularProducts $getPopularProducts
     * @param Config $configDataService
     */
    public function __construct(
        GetPopularProducts $getPopularProducts,
        Config $configDataService
    ) {
        $this->getPopularProducts = $getPopularProducts;
        $this->configDataService = $configDataService;
    }

    /**
     * Get data for template.
     *
     * @return array
     */
    public function getItems(): array
    {

        if (!$this->configDataService->isEnabled()) {
            return [];
        }
      return $this->getPopularProducts->getTopProducts();
    }

    /**
     * Set tag for page cache.
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return ['perspective_popular_products'];
    }
}
