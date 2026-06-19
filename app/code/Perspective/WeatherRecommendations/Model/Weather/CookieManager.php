<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Model\Weather;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Perspective\WeatherRecommendations\Service\WeatherConfig;

class CookieManager
{
    public const COOKIE_CITY_NAME = 'customer_city';
    public const COOKIE_LATITUDE_NAME= 'city_latitude';
    public const COOKIE_LONGITUDE_NAME= 'city_longitude';
    public const COOKIE_TEMPERATURE_NAME= 'city_temperature';

    /**
     * @var CookieManagerInterface
     */
    protected CookieManagerInterface $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected CookieMetadataFactory $cookieMetadataFactory;

    /**
     * @var SessionManagerInterface
     */
    protected SessionManagerInterface $sessionManager;

    /**
     * @var WeatherConfig
     */
    protected WeatherConfig $weatherConfig;

    /**
     * CookieManager constructor.
     *
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     * @param WeatherConfig $weatherConfig
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        WeatherConfig $weatherConfig
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->weatherConfig = $weatherConfig;
    }

    /**
     * Set public cookie with data
     *
     * @param mixed $value
     * @param string $cookieName
     * @return void
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function set(mixed $value, string $cookieName): void
    {
        $duration = $this->weatherConfig->getDataCacheTime();

        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setDuration($duration*60);

        $this->cookieManager->setPublicCookie($cookieName, $value, $metadata);
    }

    /**
     * @param string $cookieName
     * @return mixed|null
     */
    public function get(string $cookieName): mixed
    {
        $value = $this->cookieManager->getCookie($cookieName);
        return $value ? json_decode($value, true) : null;
    }
}
