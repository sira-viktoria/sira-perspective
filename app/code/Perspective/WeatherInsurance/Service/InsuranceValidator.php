<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Service;

use Perspective\WeatherRecommendations\Model\Weather\CookieManager;
use Perspective\WeatherRecommendations\Service\Validator as WeatherValidator;
use Perspective\WeatherRecommendations\Model\Weather\CookieManager as WeatherCookieManager;
use Perspective\WeatherRecommendations\Service\WeatherConfig;
use Perspective\WeatherRecommendations\Service\WeatherConfig as WeatherConfigService;

/**
 * InsuranceValidator.
 */
class InsuranceValidator
{
    /**
     * @var WeatherValidator
     */
    protected WeatherValidator $weatherValidator;

    /**
     * @var WeatherCookieManager
     */
    protected WeatherCookieManager $cookieManager;

    /**
     * @var WeatherInsuranceConfig
     */
    protected WeatherInsuranceConfig $weatherInsuranceConfig;

    /**
     * @var WeatherConfigService
     */
    protected WeatherConfig $weatherConfigService;

    /**
     * InsuranceValidator constructor.
     *
     * @param WeatherValidator $weatherValidator
     * @param WeatherCookieManager $cookieManager
     * @param WeatherInsuranceConfig $weatherInsuranceConfig
     * @param WeatherConfigService $weatherConfigService
     */
    public function __construct(
        WeatherValidator $weatherValidator,
        WeatherCookieManager $cookieManager,
        WeatherInsuranceConfig $weatherInsuranceConfig,
        WeatherConfigService $weatherConfigService
    ) {
        $this->weatherValidator = $weatherValidator;
        $this->cookieManager = $cookieManager;
        $this->weatherInsuranceConfig = $weatherInsuranceConfig;
        $this->weatherConfigService = $weatherConfigService;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->weatherValidator->isTemperatureAvailableFromApi()
            && $this->weatherInsuranceConfig->isEnabled()
            && $this->weatherInsuranceConfig->getWeatherConditions()
            && $this->isCurrentScenarioValid();
    }

    /**
     * Check if current scenario in config scenarios
     *
     * @return bool
     */
    public function isCurrentScenarioValid(): bool
    {
        $temperature = $this->cookieManager->get(CookieManager::COOKIE_TEMPERATURE_NAME);
        $currentScenario = $this->getScenarioWeather($temperature);
        $configScenarios = $this->weatherInsuranceConfig->getWeatherConditions();

        return in_array($currentScenario, $configScenarios);
    }

    /**
     * Get weather by scenario
     *
     * @param $temperature
     *
     * @return string
     */
    public function getScenarioWeather($temperature): string
    {
        $configValue = $this->weatherConfigService->getWeatherConditions();

        foreach ($configValue as $config) {
            if ($temperature != null && $temperature >= $config['temperature_from'] && $temperature <= $config['temperature_to']) {
                return $config['weather'];
            }
        }

        return '';
    }
}
