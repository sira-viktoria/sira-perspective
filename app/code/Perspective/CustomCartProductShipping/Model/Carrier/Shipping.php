<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomCartProductShipping\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Perspective\CustomCartProductShipping\Model\Config;
use Perspective\CustomCartProductShipping\Model\Service\IsAvailableCustomerByGroup;
use Psr\Log\LoggerInterface;

/**
 * Shipping Class.
 */
class Shipping extends AbstractCarrier implements
    CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'perspectiveshipping';

    /**
     * @var ResultFactory
     */
    protected ResultFactory $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected MethodFactory $_rateMethodFactory;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var IsAvailableCustomerByGroup
     */
    protected IsAvailableCustomerByGroup $isAvailableCustomerByGroup;

    /**
     * Shipping constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Config $config
     * @param IsAvailableCustomerByGroup $isAvailableCustomerByGroup
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Config $config,
        IsAvailableCustomerByGroup $isAvailableCustomerByGroup,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->config = $config;
        $this->isAvailableCustomerByGroup = $isAvailableCustomerByGroup;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return float
     */
    private function getShippingPrice(): float
    {
        $configPrice = $this->getConfigData('price');

        return $this->getFinalPriceWithHandlingFee($configPrice);
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request): Result|bool
    {
        $isAvailableCustomer = $this->isAvailableCustomerByGroup->isAvailableCustomerByGroup();
//        if (!$this->getConfigFlag('active') || !$this->config->isEnabled() || !$isAvailableCustomer) {
//            return false;
//        }

        $result = $this->_rateResultFactory->create();

        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $amount = $this->getShippingPrice();

        $finalPrice = $this->getShippingPriceWithDiscount();

        if ($finalPrice) {
            $amount = $finalPrice;
        }

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }

    /**
     * @return float
     */
    private function getShippingPriceWithDiscount(): float
    {
       return $this->config->getShippingPriceWithDiscount();
    }
}
