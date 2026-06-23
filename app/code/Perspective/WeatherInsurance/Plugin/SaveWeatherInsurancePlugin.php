<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Plugin;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * SaveWeatherInsurancePlugin.
 */
class SaveWeatherInsurancePlugin
{
    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $quoteRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * SaveWeatherInsurancePlugin constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return array
     */
    public function beforeSave(OrderRepositoryInterface $subject, OrderInterface $order): array
    {
        $quoteId = $order->getQuoteId();

        if ($quoteId) {
            try {
                $quote = $this->quoteRepository->get($quoteId);

                $insuranceAmount = $quote->getData('weather_insurance');
                $isInsurance = $quote->getData('is_weather_insurance');

                if ($isInsurance && $insuranceAmount !== null) {
                    $order->setData('weather_insurance', $insuranceAmount);
                }

            } catch (\Exception $e) {
                $this->logger->error('Error transferring custom amount from quote to order: ' . $e->getMessage());
            }
        }

        return [$order];
    }
}
