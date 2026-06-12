<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomerCustomization\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Perspective\CustomerCustomization\Model\Service\IsCustomerExistWithPhoneNumber;

/**
 * CustomerPhoneValidation Plugin.
 */
class CustomerPhoneValidation
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var IsCustomerExistWithPhoneNumber
     */
    protected IsCustomerExistWithPhoneNumber $isCustomerExist;

    /**
     * CustomerPhoneValidation constructor.
     *
     * @param RequestInterface $request
     * @param IsCustomerExistWithPhoneNumber $isCustomerExist
     */
    public function __construct(
        RequestInterface $request,
        IsCustomerExistWithPhoneNumber $isCustomerExist
    ) {
        $this->request = $request;
        $this->isCustomerExist = $isCustomerExist;
    }

    /**
     * @throws LocalizedException
     */
    public function beforeSave(
        CustomerRepositoryInterface $subject,
        CustomerInterface $customer
    ): array
    {
        $phone = trim($this->request->getParam('additional_phone', ''));

        if(!$phone && $customer->getId()) {
            $phone = $customer->getCustomAttribute('additional_phone')->getValue();
        }

        if (empty($phone)) {
            return [$customer];
        }

        foreach ($this->isCustomerExist->getCustomerListByPhone($phone)->getItems() as $existingCustomer) {
            if ((!$customer->getId() || $customer->getId() != $existingCustomer->getId()) && $this->isCustomerExist->execute($phone)) {
                throw new LocalizedException(
                    __('A customer with this phone number already exists.')
                );
            }

        }

        return [$customer];
    }
}
