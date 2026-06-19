<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Model\Weather\Recommendation;

use Perspective\WeatherRecommendations\Service\WeatherConfig as WeatherConfigService;

/**
 * Get categories.
 */
class SelectCategories
{
    /**
     * @var WeatherConfigService
     */
    protected WeatherConfigService $weatherConfigService;

    /**
     * SelectCategories constructor.
     *
     * @param WeatherConfigService $weatherConfigService
     */
    public function __construct(
        WeatherConfigService $weatherConfigService
    ) {
        $this->weatherConfigService = $weatherConfigService;
    }

    /**
     * Get categories by scenario
     *
     * @param $temperature
     *
     * @return array
     */
    public function getScenarioCategories($temperature): array
    {
        $configValue = $this->weatherConfigService->getWeatherConditions();

        foreach ($configValue as $config) {
            if ($temperature != null && $temperature >= $config['temperature_from'] && $temperature <= $config['temperature_to']) {
                return $config['categories'];
            }
        }
        return [];
    }
}
