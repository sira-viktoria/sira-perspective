<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\MyPay\Model\Service;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perspective\MyPay\Model\Config as MyPayConfig;

/**
 * RestrictShippingMethodsByCustomerGroup Class.
 */
class RestrictShippingMethodsByCustomerGroup
{
    private const FREE_SHIPPING_CODE = 'freeshipping';

    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;

    /**
     * @var MyPayConfig
     */
    protected MyPayConfig $myPayConfig;

    /**
     * @var CheckoutSession
     */
    protected CheckoutSession $checkoutSession;

    /**
     * RestrictShippingMethodsByCustomerGroup constructor.
     *
     * @param CustomerSession $customerSession
     * @param MyPayConfig $myPayConfig
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CustomerSession $customerSession,
        MyPayConfig $myPayConfig,
        CheckoutSession $checkoutSession,
    ) {
        $this->customerSession = $customerSession;
        $this->myPayConfig = $myPayConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param $result
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute($result): mixed
    {
        if (!$this->myPayConfig->isEnabled()) {
            return $result;
        }

        $currentGroupId = $this->customerSession->getCustomerGroupId();
        $largeTradeGroupId = $this->myPayConfig->getLargeTradeCustomerGroupsId();
        $tradeGroupId = $this->myPayConfig->getTradeCustomerGroupsId();

        if ($currentGroupId == $largeTradeGroupId) {
            foreach ($result as $carrierCode => $rates) {
                if ($carrierCode !== self::FREE_SHIPPING_CODE) {
                    unset($result[$carrierCode]);
                }
            }
        }

        if ($currentGroupId == $tradeGroupId) {
            $qtyOfProductsForTrade = $this->myPayConfig->getQtyOfProductsForTrade();
            $qtyOfProducts = $this->checkoutSession->getQuote()->getItemsSummaryQty();
            $allowedShippingForTrade = $this->myPayConfig->getAllowedShippingForTrade();

            foreach ($result as $carrierCode => $rates) {
                if ($carrierCode == self::FREE_SHIPPING_CODE) {
                    unset($result[$carrierCode]);
                }
                if ($carrierCode !== $allowedShippingForTrade && $qtyOfProducts > $qtyOfProductsForTrade) {
                    unset($result[$carrierCode]);
                }
            }
        }

        return $result;
    }
}
