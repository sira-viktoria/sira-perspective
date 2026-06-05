<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
namespace Perspective\MyPay\Plugin\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\MethodList;
use Magento\Quote\Api\Data\CartInterface;
use Perspective\MyPay\Model\Service\RestrictPaymentMethodsByCustomerGroup;

/**
 * PaymentMethodListRestrict Plugin.
 */
class PaymentMethodListRestrict
{
    protected RestrictPaymentMethodsByCustomerGroup $paymentRestrict;

    /**
     * PaymentMethodListFRestrict constructor.
     *
     * @param RestrictPaymentMethodsByCustomerGroup $paymentRestrict
     */
    public function __construct(
        RestrictPaymentMethodsByCustomerGroup $paymentRestrict
    ) {
        $this->paymentRestrict = $paymentRestrict;
    }

    /**
     * @param MethodList $subject
     * @param $result
     * @param CartInterface|null $quote
     * @return mixed
     */
    public function afterGetAvailableMethods(MethodList $subject, $result, ?CartInterface $quote = null): mixed
    {
        try {
            return $this->paymentRestrict->execute($result);
        } catch (NoSuchEntityException|LocalizedException $e) {
            return $result;
        }
    }
}
