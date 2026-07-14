<?php
declare(strict_types=1);

namespace Perspective\CheckoutPlaceOrderService\Model\Payment;

use Hyva\Checkout\Model\Magewire\Payment\DefaultPlaceOrderService;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;

class QtyPlaceOrderService extends DefaultPlaceOrderService
{
    /**
     * Власна перевірка кількості товарів безпосередньо перед створенням замовлення
     *
     * @param Quote $quote
     * @return int
     * @throws LocalizedException
     */
    public function placeOrder(Quote $quote): int
    {
        // 1. Перевіряємо загальну кількість одиниць товарів у кошику
        $totalQty = (float)$quote->getItemsQty();

        if ($totalQty > 20) {
            // Скасовуємо операцію. Замовлення не буде створено в БД.
            throw new LocalizedException(
                __('Orders with more than 20 items require manager approval.')
            );
        }

        // 2. Якщо все добре, викликаємо оригінальний метод для створення замовлення
        return parent::placeOrder($quote);
    }
}
