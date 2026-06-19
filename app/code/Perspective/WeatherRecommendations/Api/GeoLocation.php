<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Api;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Perspective\WeatherRecommendations\Service\JsonAsObject;
use Perspective\WeatherRecommendations\Service\WeatherConfig;
use Perspective\WeatherRecommendations\Model\Weather\CookieManager as WeatherCookieManager;

/**
 * GeoLocationService Api Service.
 */
class GeoLocation
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var WeatherConfig
     */
    protected WeatherConfig $weatherConfig;

    /**
     * @var JsonAsObject
     */
    protected JsonAsObject $jsonAsObject;

    /**
     * @var WeatherCookieManager
     */
    protected WeatherCookieManager $weatherCookieManager;

    /**
     * @var Curl
     */
    protected Curl $curl;

    /**
     * GeoLocationService constrictor.
     *
     * @param RequestInterface $request
     * @param WeatherConfig $weatherConfig
     * @param JsonAsObject $jsonAsObject
     * @param WeatherCookieManager $weatherCookieManager
     * @param Curl $curl
     */
    public function __construct(
        RequestInterface $request,
        WeatherConfig $weatherConfig,
        JsonAsObject $jsonAsObject,
        WeatherCookieManager $weatherCookieManager,
        Curl $curl
    ) {
        $this->request = $request;
        $this->weatherConfig = $weatherConfig;
        $this->jsonAsObject = $jsonAsObject;
        $this->weatherCookieManager = $weatherCookieManager;
        $this->curl = $curl;
    }

    /**
     * @return mixed
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function execute(): mixed
    {
        $url = trim($this->weatherConfig ->getGeoApiUrl(), ' ');
        $ip = $this->request->getClientIp();


        //Just for local test.  "192.168.97" means local ip
        if (str_contains($ip, "192.168.97")) {
            $ip = '176.116.93.1';
        }

        $url = $url.$ip;
        $this->curl->get($url);
        $response = $this->curl->getBody();

        return $this->jsonAsObject->unserialize($response, true);
    }

    /**
     * @return mixed|null
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    public function getCityName(): mixed
    {
        $cityFromCookie =  $this->weatherCookieManager->get(WeatherCookieManager::COOKIE_CITY_NAME);

        if (!$cityFromCookie) {
            $geoLocation = $this->execute();
            if (isset($geoLocation['city']) && $geoLocation['success'] === true) {
                $this->weatherCookieManager->set($geoLocation['city'], WeatherCookieManager::COOKIE_CITY_NAME);
                return $geoLocation['city'];
            }
        }

        return null;
    }

    /**
     * @return array
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    public function getLatitudeLongitude(): array
    {
        $latitudeFromCookie =  $this->weatherCookieManager->get(WeatherCookieManager::COOKIE_LATITUDE_NAME);
        $longitudeFromCookie =  $this->weatherCookieManager->get(WeatherCookieManager::COOKIE_LONGITUDE_NAME);

        if (!$latitudeFromCookie || !$longitudeFromCookie) {
            $geoLocation = $this->execute();
            if (isset($geoLocation['latitude']) && isset($geoLocation['longitude']) && $geoLocation['success'] === true) {
                $this->weatherCookieManager->set($geoLocation['latitude'], WeatherCookieManager::COOKIE_LATITUDE_NAME);
                $this->weatherCookieManager->set($geoLocation['longitude'], WeatherCookieManager::COOKIE_LONGITUDE_NAME);
                $latitudeFromCookie = $geoLocation['latitude'];
                $longitudeFromCookie = $geoLocation['longitude'];
            }
        }

        return ['latitude' =>$latitudeFromCookie, 'longitude' => $longitudeFromCookie];
    }
}
