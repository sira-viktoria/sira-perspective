<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomerCustomization\Controller\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Perspective\CustomerCustomization\Model\Service\IsCustomerExistWithPhoneNumber;

/**
 * CheckAdditionalPhone Class.
 */
class CheckAdditionalPhone implements HttpGetActionInterface
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /***
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var IsCustomerExistWithPhoneNumber
     */
    protected IsCustomerExistWithPhoneNumber $isCustomerExist;

    /**
     * CheckAdditionalPhone constructor.
     *
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     * @param IsCustomerExistWithPhoneNumber $isCustomerExist
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $resultJsonFactory,
        IsCustomerExistWithPhoneNumber $isCustomerExist
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->isCustomerExist = $isCustomerExist;
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $phone = trim($this->request->getParam('additional_phone', ''));

        if (empty($phone)) {
            return $result->setData(['unique' => true]);
        }

        if ($this->isCustomerExist->execute($phone)) {
            return $result->setData([
                'unique' => false,
                'message' => __('A customer with this phone number already exists.')
            ]);
        }

        return $result->setData(['unique' => true]);
    }
}
