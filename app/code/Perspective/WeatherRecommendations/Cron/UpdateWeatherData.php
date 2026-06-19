<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Cron;

use Psr\Log\LoggerInterface;

/**
 * UpdateWeatherData Cron
 */
class UpdateWeatherData
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
    public function execute()
    {
        //TODO:: Need more details.
    }
}
