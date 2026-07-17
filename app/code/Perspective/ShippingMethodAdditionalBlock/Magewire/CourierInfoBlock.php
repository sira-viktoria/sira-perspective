<?php
declare(strict_types=1);

namespace Perspective\ShippingMethodAdditionalBlock\Magewire;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magewirephp\Magewire\Component;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;

/**
 * CourierInfoBlock Magewire.
 */
class CourierInfoBlock extends Component implements EvaluationInterface
{
    public string $shippingCourierNotice = 'Courier delivers from 9:00 to 18:00';
    public string $shippingCourierDescription = '';
    public string $selectedMethod = '';
    public bool $isVisible= false;
    public const SHIPPING_METHOD= 'flatrate_flatrate';

    protected $listeners = [
        'shipping_method_selected' => 'onShippingMethodSelected'
    ];

    /**
     * @var SessionCheckout
     */
    protected SessionCheckout $checkoutSession;

    /**
     * CourierInfoBlock constructor.
     *
     * @param SessionCheckout $checkoutSession
     */
    public function __construct(
        SessionCheckout $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function mount(): void
    {
        $this->evaluateVisibility();
    }

    /**
     * @param string|null $methodCode
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function onShippingMethodSelected(?string $methodCode = null): void
    {
        if (!empty($methodCode)) {
            $this->selectedMethod = $methodCode;
        } else {
            $this->evaluateVisibility();
        }
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function evaluateVisibility(): void
    {
        $quote = $this->checkoutSession->getQuote();

        $shippingName = $this->selectedMethod;
        if ($quote && $shippingAddress = $quote->getShippingAddress()) {
            $this->selectedMethod =  $shippingAddress->getShippingMethod();
            $shippingName = $shippingAddress->getData('shipping_description');
        }

        if ($this->selectedMethod === self::SHIPPING_METHOD) {
            $this->isVisible = true;
            $this->shippingCourierDescription =  "Your shipping method is {$shippingName}";
        } else {
            $this->isVisible = false;
        }
    }

    /**
     * @param EvaluationResultFactory $resultFactory
     * @return EvaluationResultInterface
     */
    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        if ($this->selectedMethod === self::SHIPPING_METHOD) {
            $this->isVisible = true;
        }

        return $resultFactory->createSuccess();
    }
}
