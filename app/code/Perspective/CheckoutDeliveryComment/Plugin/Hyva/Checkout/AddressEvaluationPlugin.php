<?php
declare(strict_types=1);

namespace Perspective\CheckoutDeliveryComment\Plugin\Hyva\Checkout;

use Hyva\Checkout\Magewire\Checkout\AddressView\AbstractMagewireAddressForm;

/**
 * AddressEvaluationPlugin.
 */
class AddressEvaluationPlugin
{
    /**
     * @param AbstractMagewireAddressForm $subject
     * @param $resultFactory
     * @param $result
     * @return mixed
     */
    public function afterEvaluateCompletion(
        AbstractMagewireAddressForm $subject,
        $resultFactory,
        $result
    ): mixed {
        $countryId = $subject->address['country_id'] ?? null;

        if ($countryId === 'UA') {
            return $result->createCustom('orderDeliveryComment')
                ->withDetails(['showComment' => true]);
        } else {
            return $result->createCustom('orderDeliveryComment')
                ->withDetails(['showComment' => false]);
        }
    }
}
