<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\MyPay\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Model\Config as PaymentConfig;

/**
 * PaymentMethods Source Model.
 */
class PaymentMethods implements OptionSourceInterface
{
    /**
     * @var PaymentConfig
     */
    protected PaymentConfig $paymentConfig;

    /**
     * PaymentMethods constructor.
     *
     * @param PaymentConfig $paymentConfig
     */
    public function __construct(PaymentConfig $paymentConfig)
    {
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $activeMethods = $this->paymentConfig->getActiveMethods();

        foreach ($activeMethods as $code => $method) {
            $options[] = [
                'value' => $code,
                'label' => $method->getConfigData('title') ?: $code
            ];
        }

        return $options;
    }
}
