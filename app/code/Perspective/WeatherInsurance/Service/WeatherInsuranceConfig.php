<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * WeatherInsuranceConfig Model.
 */
class WeatherInsuranceConfig
{
    /**
     * Paths to Configurations.
     */
    private const XML_PATH_WEATHER_INSURANCE_GENERAL_SETTINGS = 'weather_insurance/general_settings';
    private const XML_PATH_WEATHER_INSURANCE_ENABLED = 'weather_insurance/general_settings/enabled';
    private const XML_PATH_WEATHER_INSURANCE_BASE_PRICE = 'weather_insurance/general_settings/base_insurance_price';
    private const XML_PATH_WEATHER_INSURANCE_LABEL_TEXT = 'weather_insurance/general_settings/label_text';
    private const XML_PATH_WEATHER_INSURANCE_DESCRIPTION = 'weather_insurance/general_settings/description';
    private const XML_PATH_WEATHER_INSURANCE_CONDITIONS = 'weather_insurance/general_settings/weather_conditions';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * WeatherConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_WEATHER_INSURANCE_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getBaseInsurancePrice(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_INSURANCE_BASE_PRICE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getInsuranceLabelText(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_INSURANCE_LABEL_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getInsuranceDescription(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEATHER_INSURANCE_DESCRIPTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getWeatherConditions(): array
    {
        $rules = $this->scopeConfig->getValue(self::XML_PATH_WEATHER_INSURANCE_CONDITIONS, ScopeInterface::SCOPE_STORE);

        try {
            $rules = explode(',', $rules);
        } catch (\Exception $e) {
            $rules = [];
        }

        if (!is_array($rules) || empty($rules)) {
            return [];
        }

        return $rules;
    }
}
