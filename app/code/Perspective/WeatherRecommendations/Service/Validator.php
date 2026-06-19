<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Service;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Perspective\WeatherRecommendations\Api\GeoLocation;
use Perspective\WeatherRecommendations\Api\OpenWeatherMap;
use Perspective\WeatherRecommendations\Model\Weather\CookieManager as CookieManager;
use Perspective\WeatherRecommendations\Service\WeatherConfig as WeatherConfigService;

/**
 * Check if the widget is visible.
 */
class Validator
{
    /**
     * @var CookieManager
     */
    protected CookieManager $cookieManager;

    /**
     * @var WeatherConfigService
     */
    protected WeatherConfig $weatherConfigService;

    /**
     * @var GeoLocation
     */
    protected GeoLocation $geoLocation;

    /**
     * @var OpenWeatherMap
     */
    protected OpenWeatherMap $openWeatherMap;

    /**
     * Validator constructor.
     *
     * @param CookieManager $cookieManager
     * @param WeatherConfigService $weatherConfigService
     * @param GeoLocation $geoLocation
     * @param OpenWeatherMap $openWeatherMap
     */
    public function __construct(
        CookieManager $cookieManager,
        WeatherConfigService $weatherConfigService,
        GeoLocation $geoLocation,
        OpenWeatherMap $openWeatherMap
    ) {
        $this->cookieManager = $cookieManager;
        $this->weatherConfigService = $weatherConfigService;
        $this->geoLocation = $geoLocation;
        $this->openWeatherMap = $openWeatherMap;
    }

    /**
     * @return bool
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function validate(): bool
    {
        return $this->weatherConfigService->isEnabled()
            && $this->isModuleConfigured()
            && $this->isCityAvailableFromApi()
            && $this->isTemperatureAvailableFromApi();
    }

    /**
     * @return bool
     */
    public function isModuleConfigured(): bool
    {
        $configGeneralData = $this->weatherConfigService->getGeneralConfig();

        if (
            !isset($configGeneralData) ||
            empty($configGeneralData['weather_api_key']) ||
            empty($configGeneralData['weather_api_url']) ||
            empty($configGeneralData['geo_api_url'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function isCityAvailableFromApi(): bool
    {
        if ($this->geoLocation->getCityName()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isTemperatureAvailableFromApi(): bool
    {
        if ($this->openWeatherMap->getTemperature()) {
            return true;
        }
        return false;
    }
}
