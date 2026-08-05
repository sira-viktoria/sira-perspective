<?php
declare(strict_types=1);

namespace Perspective\Reservation\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Check Customer by email.
 */
class CheckCustomer implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $jsonFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     *
     * CheckCustomer constructor.
     *
     * @param RequestInterface $request
     * @param JsonFactory $jsonFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $jsonFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute(): Json|ResultInterface|ResponseInterface
    {
        $resultJson = $this->jsonFactory->create();
        $email = $this->request->getParam('email');

        try {
            $customer = $this->customerRepository->get($email);
            $billingAddress = $customer->getDefaultBillingAddress();

            return $resultJson->setData([
                'exists' => true,
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'telephone' => $billingAddress ? $billingAddress->getTelephone() : ''
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData(['exists' => false]);
        }
    }
}
