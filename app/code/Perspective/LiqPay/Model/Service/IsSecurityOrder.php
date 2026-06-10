<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Model\Service;

use Magento\Payment\Helper\Data as PaymentHelper;
use Perspective\LiqPay\Model\Config as PerspectiveConfig;

/**
 * IsLiqPayPaymentMethod Class.
 */
class IsSecurityOrder
{
    /**
     * @var PaymentHelper
     */
    protected PaymentHelper $paymentHelper;

    protected PerspectiveConfig $perspectiveConfig;


    /**
     * @param PaymentHelper $paymentHelper
     * @param PerspectiveConfig $perspectiveConfig
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        PerspectiveConfig $perspectiveConfig
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->perspectiveConfig = $perspectiveConfig;
    }

    /**
     * @param $data
     * @param $receivedPublicKey
     * @param $receivedSignature
     * @return bool
     */
    public function execute($data, $receivedPublicKey, $receivedSignature): bool
    {
        if ($this->perspectiveConfig->isSecurity()) {
            $publicKey = $this->perspectiveConfig->getPublicKey();
            if ($publicKey !== $receivedPublicKey) {
                return false;
            }
            $privateKey = $this->perspectiveConfig->getPrivateKey();

            $generatedSignature = base64_encode(sha1($privateKey . $data . $privateKey, true));
            return $receivedSignature === $generatedSignature;
        } else {
            return true;
        }
    }
}
