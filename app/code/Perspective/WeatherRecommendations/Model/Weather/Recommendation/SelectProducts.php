<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Model\Weather\Recommendation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Perspective\WeatherRecommendations\Api\GeoLocation;
use Perspective\WeatherRecommendations\Api\OpenWeatherMap;
use Perspective\WeatherRecommendations\Service\WeatherConfig;

/**
 * Get products.
 */
class SelectProducts
{
    /**
     * @var SelectCategories
     */
    protected SelectCategories $selectCategories;

    /**
     * @var WeatherConfig
     */
    protected WeatherConfig $weatherConfig;

    /**
     * @var GeoLocation
     */
    protected GeoLocation $geoLocation;

    /**
     * @var OpenWeatherMap
     */
    protected OpenWeatherMap $openWeatherMap;
    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected FilterBuilder $filterBuilder;

    /**
     * SelectProducts constructor.
     *
     * @param SelectCategories $selectCategories
     * @param WeatherConfig $weatherConfig
     * @param GeoLocation $geoLocation
     * @param OpenWeatherMap $openWeatherMap
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        SelectCategories $selectCategories,
        WeatherConfig $weatherConfig,
        GeoLocation $geoLocation,
        OpenWeatherMap $openWeatherMap,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
    ) {
        $this->selectCategories = $selectCategories;
        $this->weatherConfig = $weatherConfig;
        $this->geoLocation = $geoLocation;
        $this->openWeatherMap = $openWeatherMap;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @return array
     */
    public function getRandomSalableSkus (): array
    {
        if ($this->openWeatherMap->getTemperature() === null) {
            return [];
        }

        $products = $this->getProducts();
        $limit = $this->weatherConfig->getNumberOfProducts();

        $seed = (int)floor(date('YmdHi') / 30);
        mt_srand($seed);
        shuffle($products);

        return array_slice($products, 0, $limit);
    }

    /**
     * @return ProductInterface[]
     */
    public function getProducts(): array
    {
        $categoryIds = $this->selectCategories->getScenarioCategories($this->openWeatherMap->getTemperature());

        if (!$categoryIds) {
            return [];
        }

        $categoryFilter = $this->filterBuilder
            ->setField('category_id')
            ->setConditionType('in')
            ->setValue([$categoryIds])
            ->create();

        $statusFilter = $this->filterBuilder
            ->setField('status')
            ->setConditionType('eq')
            ->setValue(Status::STATUS_ENABLED)
            ->create();

        $visibleFilter = $this->filterBuilder
            ->setField('visibility')
            ->setConditionType('eq')
            ->setValue(Visibility::VISIBILITY_BOTH)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$categoryFilter])
            ->addFilters([$statusFilter])
            ->addFilters([$visibleFilter])
            ->setPageSize(20)
            ->create();

        $searchResult = $this->productRepository->getList($searchCriteria);
        return $searchResult->getItems();
    }
}
