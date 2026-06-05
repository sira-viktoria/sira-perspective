<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
namespace Perspective\MyPay\Plugin\Model\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;
use Perspective\MyPay\Model\Service\RestrictShippingMethodsByCustomerGroup;

/**
 * AddressRestrict Plugin.
 */
class AddressRestrict
{
    /**
     * @var RestrictShippingMethodsByCustomerGroup
     */
    protected RestrictShippingMethodsByCustomerGroup $restrictShipping;

    /**
     * AddressRestrict constructor.
     *
     * @param RestrictShippingMethodsByCustomerGroup $restrictShipping
     */
    public function __construct(

        RestrictShippingMethodsByCustomerGroup $restrictShipping
    ) {
        $this->restrictShipping = $restrictShipping;
    }

    /**
     * @param Address $subject
     * @param $result
     * @return mixed
     */
    public function afterGetGroupedAllShippingRates(Address $subject, $result): mixed
    {
        try {
            return $this->restrictShipping->execute($result);
        } catch (NoSuchEntityException|LocalizedException $e) {
            return $result;
        }
    }
}
