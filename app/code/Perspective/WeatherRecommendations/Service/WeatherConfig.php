<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Config Model.
 */
class WeatherConfig
{
    /**
     * Paths to Configurations.
     */
    private const XML_PATH_WEATHER_WIDGET_GENERAL_SETTINGS = 'weather_widget/general_settings';
    private const XML_PATH_WEATHER_WIDGET_WEATHER_SETTINGS = 'weather_widget/weather_settings';
    private const XML_PATH_WEATHER_WIDGET_ENABLED = 'weather_widget/general_settings/enabled';
    private const XML_PATH_WEATHER_WIDGET_NUMBER_OF__PRODUCTS = 'weather_widget/general_settings/products_number';
    private const XML_PATH_WEATHER_WIDGET_DATA_CAHE_TIME = 'weather_widget/general_settings/data_cache_time';
    private const XML_PATH_WEATHER_WIDGET_API_KEY= 'weather_widget/general_settings/weather_api_key';
    private const XML_PATH_WEATHER_WIDGET_API_URL = 'weather_widget/general_settings/weather_api_url';
    private const XML_PATH_WEATHER_WIDGET_GEO_API_URL= 'weather_widget/general_settings/geo_api_url';
    private const XML_PATH_WEATHER_WIDGET_CONDITIONS = 'weather_widget/weather_settings/conditions';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * WeatherConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_WEATHER_WIDGET_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getDataCacheTime(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_WIDGET_DATA_CAHE_TIME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getNumberOfProducts(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_WEATHER_WIDGET_NUMBER_OF__PRODUCTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getApiKey(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_WIDGET_API_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getApiUrl(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_WIDGET_API_URL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getGeoApiUrl(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_WIDGET_GEO_API_URL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getWeatherConditions(): array
    {
        $rulesJson = $this->scopeConfig->getValue(self::XML_PATH_WEATHER_WIDGET_CONDITIONS, ScopeInterface::SCOPE_STORE);

        try {
            $rules = $this->serializer->unserialize($rulesJson);
        } catch (\Exception $e) {
            $rules = [];
        }

        if (!is_array($rules) || empty($rules)) {
            return [];
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function getGeneralConfig(): array
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_WIDGET_GENERAL_SETTINGS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getWeatherConfig(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_WIDGET_WEATHER_SETTINGS, ScopeInterface::SCOPE_STORE);
    }
}
