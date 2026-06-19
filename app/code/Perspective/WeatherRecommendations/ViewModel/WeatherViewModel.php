<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\ViewModel;

use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Perspective\WeatherRecommendations\Api\GeoLocation;
use Perspective\WeatherRecommendations\Api\OpenWeatherMap;
use Perspective\WeatherRecommendations\Service\Validator as ValidatorWeather;
use Perspective\WeatherRecommendations\Service\WeatherConfig;
use Perspective\WeatherRecommendations\Model\Weather\Recommendation\SelectProducts;

/**
 * WeatherViewModel.
 */
class WeatherViewModel implements ArgumentInterface
{
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
     * @var ImageHelper
     */
    private ImageHelper $imageHelper;

    /**
     * @var LayoutInterface
     */
    protected LayoutInterface $layout;

    /**
     * @var ProductUrlPathGenerator
     */
    protected ProductUrlPathGenerator $productUrlPathGenerator;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var SelectProducts
     */
    protected SelectProducts $selectProducts;

    /**
     * @var ValidatorWeather
     */
    protected ValidatorWeather $validatorWeather;

    /**
     * WeatherViewModel constructor.
     *
     * @param WeatherConfig $weatherConfig
     * @param GeoLocation $geoLocation
     * @param OpenWeatherMap $openWeatherMap
     * @param ImageHelper $imageHelper
     * @param LayoutInterface $layout
     * @param ProductUrlPathGenerator $productUrlPathGenerator
     * @param StoreManagerInterface $storeManager
     * @param SelectProducts $selectProducts
     * @param ValidatorWeather $validatorWeather
     */
    public function __construct(
        WeatherConfig $weatherConfig,
        GeoLocation $geoLocation,
        OpenWeatherMap $openWeatherMap,
        ImageHelper $imageHelper,
        LayoutInterface $layout,
        ProductUrlPathGenerator $productUrlPathGenerator,
        StoreManagerInterface $storeManager,
        SelectProducts $selectProducts,
        ValidatorWeather $validatorWeather
    ) {
        $this->weatherConfig = $weatherConfig;
        $this->geoLocation = $geoLocation;
        $this->openWeatherMap = $openWeatherMap;
        $this->imageHelper = $imageHelper;
        $this->layout = $layout;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->storeManager = $storeManager;
        $this->selectProducts = $selectProducts;
        $this->validatorWeather = $validatorWeather;
    }

    /**
     * @return bool
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function isWidgetVisible(): bool
    {
        return $this->validatorWeather->validate();
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function getTitle(): string
    {
        return sprintf(
            'In your city %s you have %d°. We recommend:',
            $this->geoLocation ->getCityName(),
            $this->openWeatherMap->getTemperature()
        );
    }

    /**
     * @return array
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function getProducts(): array
    {
        return $this->selectProducts->getRandomSalableSkus();
    }

    /**
     * Return HTML block with price
     *
     * @param Product $product
     * @return string
     */
    public function getProductPrice(Product $product): string
    {
        return $this->getProductPriceHtml(
            $product,
            FinalPrice::PRICE_CODE,
            Render::ZONE_ITEM_LIST
        );
    }

    /**
     * @param Product $product
     * @param $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        Product $product,
                $priceType,
        string  $renderZone = Render::ZONE_ITEM_LIST,
        array   $arguments = []
    ): string
    {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var Render $priceRender */
        $priceRender = $this->layout->getBlock('product.price.render.default');
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }
        return $price;
    }

    /**
     * @param $product
     * @param string $imageId Наприклад: 'category_page_grid', 'product_page_image_medium'
     * @return ImageHelper
     */
    public function getImage($product, string $imageId = 'category_page_grid'): ImageHelper
    {
        return $this->imageHelper->init($product, $imageId);
    }

    /**
     * @param $product
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductUrl($product): string
    {
        $urlPath = $this->productUrlPathGenerator->getUrlPathWithSuffix($product, $this->getCurrentStoreId());
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        return $baseUrl . $urlPath;
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}


