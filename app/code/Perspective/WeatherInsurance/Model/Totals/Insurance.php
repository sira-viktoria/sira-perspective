<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Model\Totals;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Framework\Phrase;
use Perspective\WeatherInsurance\Service\InsuranceValidator as InsuranceValidationService;
use Perspective\WeatherInsurance\Service\WeatherInsuranceConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Insurance Class.
 */
class Insurance extends AbstractTotal
{
    public const INSURANCE_TOTAL_TITLE = 'Insurance Total';
    public const INSURANCE_TOTAL_CODE = 'perspective_weather_insurance_total';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var InsuranceValidationService
     */
    protected InsuranceValidationService $insuranceValidationService;

    /**
     * @var WeatherInsuranceConfig
     */
    protected WeatherInsuranceConfig $weatherInsuranceConfig;

    /**
     * Insurance constructor.
     *
     * @param InsuranceValidationService $insuranceValidationService
     * @param ScopeConfigInterface $scopeConfig
     * @param WeatherInsuranceConfig $weatherInsuranceConfig
     */
    public function __construct(
        InsuranceValidationService $insuranceValidationService,
        ScopeConfigInterface $scopeConfig,
        WeatherInsuranceConfig $weatherInsuranceConfig,
    ) {
        $this->insuranceValidationService = $insuranceValidationService;
        $this->scopeConfig = $scopeConfig;
        $this->weatherInsuranceConfig = $weatherInsuranceConfig;
        $this->setCode(self::INSURANCE_TOTAL_CODE);
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote                       $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total                       $total
    ): Insurance
    {
        parent::collect($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $insurancePrice = $this->getInsurancePrice($quote);

        $total->addTotalAmount(self::INSURANCE_TOTAL_CODE, $insurancePrice);
        $total->addBaseTotalAmount(self::INSURANCE_TOTAL_CODE, $insurancePrice);

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total): array
    {
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => $this->getInsurancePrice($quote),
        ];
    }

    /**
     * @return Phrase
     */
    public function getLabel(): Phrase
    {
        return __(self::INSURANCE_TOTAL_TITLE);
    }

    /**
     * return correct price if conditions valid and checkbox checked
     *
     * @param Quote $quote
     * @return string
     */
    private function getInsurancePrice(Quote $quote): string
    {
        $price = '0';
        if ($this->insuranceValidationService->validate() &&
            $this->isInsuranceCheckboxEnabled($quote)
        ) {
            $price = $this->weatherInsuranceConfig->getBaseInsurancePrice();
        }
        return $price;
    }

    /**
     * @param $quote
     * @return bool
     */
    public function isInsuranceCheckboxEnabled($quote): bool
    {
        return (bool)$quote->getData('is_weather_insurance');
    }
}
