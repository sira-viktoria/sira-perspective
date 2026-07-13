<?php
declare(strict_types=1);

namespace Perspective\CheckoutDeliveryComment\Plugin\Hyva\Checkout;

use Hyva\Checkout\Magewire\Checkout\AddressView\AbstractMagewireAddressForm;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Magento\Framework\View\LayoutInterface;
/**
 * AddressEvaluationPlugin.
 */
class AddressEvaluationPlugin
{
    /**
     * @var LayoutInterface
     */
    protected LayoutInterface $layout;

    /**
     * @param LayoutInterface $layout
     */
    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param AbstractMagewireAddressForm $subject
     * @param EvaluationResult $result
     * @param $resultFactory
     *
     * @return EvaluationResult
     */
    public function afterEvaluateCompletion(
        AbstractMagewireAddressForm $subject,
        EvaluationResult $result,
        $resultFactory,
    ): EvaluationResult {


        $commentBlockName = 'checkout.default.comment';

        if (!$this->layout->hasElement($commentBlockName)) {
            return $result;
        }
        if (!isset($subject->address)) {
            return $result;
        }
        $countryId = $subject->address['country_id'] ?? null;

        if ($countryId === 'UA') {
            return $resultFactory->createCustom('orderDeliveryComment')
                ->withDetails(['showComment' => true]);
        } else {
            return $resultFactory->createCustom('orderDeliveryComment')
                ->withDetails(['showComment' => false]);
        }
    }
}
