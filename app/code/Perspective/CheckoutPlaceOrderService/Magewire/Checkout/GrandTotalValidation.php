<?php
declare(strict_types=1);

namespace Perspective\CheckoutPlaceOrderService\Magewire\Checkout;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magewirephp\Magewire\Component;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Perspective\CheckoutPlaceOrderService\Service\GrandTotalValidationService;
/**
 * GrandTotalValidation Magewire.
 */
class GrandTotalValidation extends Component implements EvaluationInterface
{
    public bool $isVisibleErrorNotice= false;
    public float $minOrderAmount = 500.00;

    protected $listeners = [
        'shipping_method_selected' => 'onShippingMethodSelected'
    ];

    /**
     * @var GrandTotalValidationService
     */
    protected GrandTotalValidationService $validationService;

    /**
     * @var EvaluationResultFactory
     */
    protected EvaluationResultFactory $resultFactory;

    /**
     * GrandTotalValidation constructor.
     *
     * @param GrandTotalValidationService $validationService
     * @param EvaluationResultFactory $resultFactory
     */
    public function __construct(
        GrandTotalValidationService $validationService,
        EvaluationResultFactory $resultFactory
    ) {
        $this->validationService = $validationService;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function boot(): void
    {
        $this->minOrderAmount = $this->validationService->getMaxLimit();
        $this->evaluateValidationState();
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function mount(): void
    {
        $this->evaluateValidationState();
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
        $this->evaluateValidationState();
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function update(): void
    {
        $this->evaluateValidationState();
    }

    /**
     * @param EvaluationResultFactory $resultFactory
     *
     * @return EvaluationResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        if (!$this->validationService->isAvailableForOrder()) {
            $this->isVisibleErrorNotice = true;
            return $resultFactory->createCustom('grandTotalValidation')
                ->withDetails(['disabled' => true]);

        }
        $this->isVisibleErrorNotice = false;
        return $resultFactory->createCustom('grandTotalValidation')
            ->withDetails(['disabled' => false]);
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function evaluateValidationState(): void
    {
        if (!$this->validationService->isAvailableForOrder()) {
            $this->resultFactory->createCustom('grandTotalValidation')
                ->withDetails(['disabled' => true]);
            $this->isVisibleErrorNotice = true;
        } else {
            $this->resultFactory->createCustom('grandTotalValidation')
                ->withDetails(['disabled' => false]);
            $this->isVisibleErrorNotice = false;
        }
    }
}
