<?php
declare(strict_types=1);

namespace Perspective\CheckoutDeliveryComment\Magewire\Checkout;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magewirephp\Magewire\Component;

/**
 * OrderDeliveryComment Magewire.
 */
class OrderDeliveryComment extends Component
{
    public string $orderDeliveryComment = '';
    public bool $saved = false;

    /**
     * @var SessionCheckout
     */
    protected SessionCheckout $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $quoteRepository;

    /**
     * OrderDeliveryComment
     *
     * @param SessionCheckout $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        SessionCheckout $checkoutSession,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
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
        if (preg_match('/' . preg_quote('test', '/') . '/i', $value)) {
            $this->saved = false;
            $this->orderDeliveryComment = $value;
            $this->dispatchBrowserEvent('checkout:order-delivery-comment:toggle-place-order', ['disabled' => true]);
            throw new LocalizedException(
                __('Server Check: Delivery Comment must not contain the word "test".')
            );
        } else {
            $this->saved = true;
            $this->dispatchBrowserEvent('checkout:order-delivery-comment:toggle-place-order', ['disabled' => false]);
        }

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
            $quote = $this->checkoutSession->getQuote();
            $quote->setData('order_delivery_comment', $value);

            $this->quoteRepository->save($quote);
            $this->saved = true;

        } catch (\Exception $e) {
            throw new LocalizedException(__('Could not save comment.'));
        }
    }
}
