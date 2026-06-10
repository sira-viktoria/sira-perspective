<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderInterface;
use Perspective\LiqPay\Model\Payment\LiqPay as LiqPayPayment;

/**
 * IsLiqPayPaymentMethod Class.
 */
class IsLiqPayPaymentMethod
{
    /**
     * @var PaymentHelper
     */
    protected PaymentHelper $paymentHelper;

    /**
     * IsLiqPayPaymentMethod constructor.
     *
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        PaymentHelper $paymentHelper,
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @param OrderInterface $order
     * @return bool
     * @throws LocalizedException
     */
    public function execute(OrderInterface $order): bool
    {
        $method = $order->getPayment()->getMethod();
        $methodInstance = $this->paymentHelper->getMethodInstance($method);

        return $methodInstance instanceof LiqPayPayment;
    }
}
