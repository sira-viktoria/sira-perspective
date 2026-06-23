<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perspective\WeatherInsurance\Service\WeatherInsuranceConfig;
use Perspective\WeatherInsurance\Service\InsuranceValidator;
use Perspective\WeatherRecommendations\Api\OpenWeatherMap;

/**
 * InsuranceConfigProvider.
 */
class InsuranceConfigProvider implements ConfigProviderInterface
{
    /**
     * @var WeatherInsuranceConfig
     */
    protected WeatherInsuranceConfig $weatherInsuranceConfig;

    /**
     * @var InsuranceValidator
     */
    protected InsuranceValidator $insuranceValidator;

    /**
     * @var CheckoutSession
     */
    protected CheckoutSession $checkoutSession;

    /**
     * @var OpenWeatherMap
     */
    protected OpenWeatherMap $openWeatherMap;

    /**
     * InsuranceConfigProvider constructor.
     *
     * @param WeatherInsuranceConfig $weatherInsuranceConfig
     * @param InsuranceValidator $insuranceValidator
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        WeatherInsuranceConfig $weatherInsuranceConfig,
        InsuranceValidator $insuranceValidator,
        CheckoutSession $checkoutSession,
        OpenWeatherMap $openWeatherMap
    ) {
        $this->weatherInsuranceConfig = $weatherInsuranceConfig;
        $this->insuranceValidator = $insuranceValidator;
        $this->checkoutSession = $checkoutSession;
        $this->openWeatherMap = $openWeatherMap;
    }

    /**
     * @return array[]
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getConfig(): array
    {
        $quote = $this->checkoutSession->getQuote();

        return [
            'weatherInsurance' => [
                'isVisible' => (bool)$this->insuranceValidator->validate(),
                'isDefaultChecked' => (bool) $quote->getData('is_weather_insurance'),
                'insurancePrice' => $this->weatherInsuranceConfig->getBaseInsurancePrice(),
                'textLabel' => $this->weatherInsuranceConfig->getInsuranceLabelText(),
                'description' => $this->weatherInsuranceConfig->getInsuranceDescription(),
                'temperature' =>  $this->openWeatherMap->getTemperature()
            ]
        ];
    }
}
