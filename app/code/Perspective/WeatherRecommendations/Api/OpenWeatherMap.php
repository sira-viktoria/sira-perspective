<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Api;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Perspective\WeatherRecommendations\Model\Weather\CookieManager as WeatherCookieManager;
use Perspective\WeatherRecommendations\Service\JsonAsObject;
use Perspective\WeatherRecommendations\Service\WeatherConfig as WeatherConfigService;

/**
 * OpenWeatherMap api Service.
 */
class OpenWeatherMap
{
    /**
     * @var Curl
     */
    protected Curl $curl;

    /**
     * @var WeatherConfigService
     */
    protected WeatherConfigService $weatherConfigService;


    /**
     * @var JsonAsObject
     */
    protected JsonAsObject $jsonAsObject;

    /**
     * @var WeatherCookieManager
     */
    protected WeatherCookieManager $weatherCookieManager;

    /**
     * @var GeoLocation
     */
    protected GeoLocation $geoLocation;

    /**
     * OpenWeatherMap constructor.
     *
     * @param Curl $curl
     * @param GeoLocation $geoLocation
     * @param JsonAsObject $jsonAsObject
     * @param WeatherConfigService $weatherConfigService
     * @param WeatherCookieManager $weatherCookieManager
     */
    public function __construct(
        Curl $curl,
        GeoLocation $geoLocation,
        JsonAsObject $jsonAsObject,
        WeatherConfigService $weatherConfigService,
        WeatherCookieManager $weatherCookieManager
    ) {
        $this->curl = $curl;
        $this->geoLocation = $geoLocation;
        $this->jsonAsObject = $jsonAsObject;
        $this->weatherConfigService = $weatherConfigService;
        $this->weatherCookieManager = $weatherCookieManager;
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function sendRequest()
    {
        $apiKey = $this->weatherConfigService->getApiKey();
        $apiUrl = $this->weatherConfigService->getApiUrl();
        $latitudeLongitude = $this->geoLocation->getLatitudeLongitude();

        $url = sprintf(
            '%s?lat=%s&lon=%s&appid=%s',
            $apiUrl,
            $latitudeLongitude['latitude'],
            $latitudeLongitude['longitude'],
            $apiKey
        );

        //Just for local  test.  "2.5" means free api service version
        if (str_contains($apiUrl, "2.5")) {
            $url = $url.'&id=524901';
        }

        $this->curl->get($url);
        $weatherData = $this->jsonAsObject->unserialize($this->curl->getBody(), true);
        if (isset($weatherData['cod']) && $weatherData['cod'] != 200 && !isset($weatherData['list'])) {
            throw new  LocalizedException(__('Weather API. Weather data not found'));
        }

        return $weatherData;
    }

    /**
     * @return int
     */
    public function getTemperature()
    {
        $tempFromCookie =  $this->weatherCookieManager->get(WeatherCookieManager::COOKIE_TEMPERATURE_NAME);

        try {
            if (!$tempFromCookie) {
                $weatherData = $this->sendRequest();
                $tempKelvin = $weatherData['list'][0]['main']['temp'];
                $tempFromCookie = $tempKelvin - 273.15;
                $this->weatherCookieManager->set($tempFromCookie, WeatherCookieManager::COOKIE_TEMPERATURE_NAME);
            }
        }
        catch (\Exception $e) {
            return null;
        }

        return (int)$tempFromCookie;
    }
}
