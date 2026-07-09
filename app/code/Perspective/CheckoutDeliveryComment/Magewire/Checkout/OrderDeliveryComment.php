<?php
declare(strict_types=1);

namespace Perspective\CheckoutDeliveryComment\Magewire\Checkout;

use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magewirephp\Magewire\Component;

/**
 * OrderDeliveryComment Magewire.
 */
class OrderDeliveryComment extends Component implements EvaluationInterface
{
    public string $orderDeliveryComment = '';
    public bool $saved = false;
    public bool $isBlockCartButton = false;

    /**
     * @var SessionCheckout
     */
    protected SessionCheckout $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $quoteRepository;

    /**
     * @var EvaluationResultFactory
     */
    protected EvaluationResultFactory $evaluationResultFactory;

    /**
     * OrderDeliveryComment
     *
     * @param SessionCheckout $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param EvaluationResultFactory $evaluationResultFactory
     */
    public function __construct(
        SessionCheckout $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        EvaluationResultFactory $evaluationResultFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->evaluationResultFactory = $evaluationResultFactory;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function mount(): void
    {
        $quote = $this->checkoutSession->getQuote();
        $this->orderDeliveryComment = (string)$quote->getData('order_delivery_comment');
    }

    /**
     * @param $value
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function updatingOrderDeliveryComment($value): mixed
    {
        $this->saveOrderDeliveryComment($value);
        return $value;
    }

    /**
     * @param $value
     *
     * @return void
     * @throws LocalizedException
     */
    private function saveOrderDeliveryComment($value): void
    {
        try {
            if (preg_match('/' . preg_quote('test', '/') . '/i', $value)) {
                $this->isBlockCartButton = true;
                $this->saved = false;
            } else {
                $quote = $this->checkoutSession->getQuote();
                $quote->setData('order_delivery_comment', $value);
                $this->quoteRepository->save($quote);
                $this->isBlockCartButton = false;
                $this->saved = true;
            }

        } catch (\Exception $e) {
            throw new LocalizedException(__('Could not save comment.'));
        }
    }

    /**
     * @param EvaluationResultFactory $resultFactory
     *
     * @return EvaluationResult
     */
    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResult
    {
        if (preg_match('/' . preg_quote('test', '/') . '/i', (string)$this->orderDeliveryComment)) {

            $this->isBlockCartButton = true;
            return $resultFactory->createBlocking();
        }

        return $resultFactory->createSuccess();
    }
}
