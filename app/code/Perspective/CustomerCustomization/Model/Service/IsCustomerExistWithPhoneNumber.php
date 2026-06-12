<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomerCustomization\Model\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * IsCustomerExistWithPhoneNumber Service.
 */
class IsCustomerExistWithPhoneNumber
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * IsCustomerExistWithPhoneNumber constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param $phone
     * @return bool
     * @throws LocalizedException
     */
    public function execute($phone): bool
    {
        if (empty($phone)) {
            return false;
        }

        return count($this->getCustomerListByPhone($phone)->getItems()) > 0;
    }

    /**
     * @param $phone
     * @return CustomerSearchResultsInterface
     * @throws LocalizedException
     */
    public function getCustomerListByPhone($phone): CustomerSearchResultsInterface
    {

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('additional_phone', $phone, 'eq')
            ->create();

        return $this->customerRepository->getList($searchCriteria);
    }
}
