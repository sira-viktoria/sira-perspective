<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Perspective\WeatherRecommendations\Service\WeatherConfig;

/*
 * WeatherOptions Class.
 */
class WeatherOptions implements OptionSourceInterface
{
    /**
     * @var WeatherConfig
     */
    protected WeatherConfig $weatherConfig;

    /**
     * WeatherOptions constructor.
     *
     * @param WeatherConfig $weatherConfig
     */
    public function __construct(
        WeatherConfig $weatherConfig
    ) {
        $this->weatherConfig = $weatherConfig;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $conditions = $this->weatherConfig->getWeatherConditions();

        $result = [];
        foreach ($conditions as $condition) {
            $result[] = [
                'label' => __($condition['weather']),
                'value' => $condition['weather'],
            ];
        }

        return $result;
    }
}
