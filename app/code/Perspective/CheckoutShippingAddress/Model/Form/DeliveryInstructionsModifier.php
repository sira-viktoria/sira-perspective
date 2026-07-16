<?php
declare(strict_types=1);

namespace Perspective\CheckoutShippingAddress\Model\Form;

use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * DeliveryInstructionsModifier Class.
 */
class DeliveryInstructionsModifier implements EntityFormModifierInterface
{
    /**
     * @var CheckoutSession.
     */
    protected CheckoutSession $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $quoteRepository;

    /**
     * DeliveryInstructionsModifier constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param EntityFormInterface $form
     * @return EntityFormInterface
     */
    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'delivery_instructions_init',
            'form:init',
            [$this, 'addDeliveryInstructionsField']
        );

        $form->registerModificationListener(
            'delivery_instructions_save',
            'form:shipping:updated',
            [$this, 'saveDeliveryInstructionsField']
        );

        return $form;
    }

    /**
     * @param EntityFormInterface $form
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addDeliveryInstructionsField(EntityFormInterface $form): void
    {
        $deliveryField = $form->hasField('delivery_instructions')
            ? $form->getField('delivery_instructions')
            : $form->createField('delivery_instructions', 'text');

        $deliveryField->setLabel(__('Delivery Instructions'));
        $deliveryField->setData('auto_save', true);

        $quote = $this->checkoutSession->getQuote();
        $deliveryField->setData('value', $quote->getData('delivery_instructions'));

        $form->addField($deliveryField);
    }

    /**
     * @param EntityFormInterface $form
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function saveDeliveryInstructionsField(EntityFormInterface $form): void
    {
        if (!$form->hasField('delivery_instructions')) {
            return;
        }

        $value = $form->getField('delivery_instructions')->getValue();
        $quote = $this->checkoutSession->getQuote();

        if ($quote) {
            $quote->setData('delivery_instructions', $value);
            $this->quoteRepository->save($quote);
        }
    }
}
