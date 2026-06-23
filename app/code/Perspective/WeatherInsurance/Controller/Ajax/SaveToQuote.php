<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Controller\Ajax;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * SaveToQuote Ajax controller.
 */
class SaveToQuote implements HttpPostActionInterface
{
    /**
     * @var CheckoutSession
     */
    protected CheckoutSession $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $quoteRepository;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * SaveToQuote constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        JsonFactory $resultJsonFactory,
        RequestInterface $request
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $insuranceCheckboxValue = (int)$this->request->getParam('insurance_checkbox_state');
        $insurancePrice = (int)$this->request->getParam('insurance_price');
        $result = $this->resultJsonFactory->create();
        try {
            $quote = $this->checkoutSession->getQuote();
            $quote->setData('is_weather_insurance', $insuranceCheckboxValue);
            $quote->setData('weather_insurance', $insurancePrice);
            $this->quoteRepository->save($quote);

        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }

        return $result->setData(['success' => true, 'message' => 'Status updated']);
    }
}
