<?php
declare(strict_types=1);

namespace Perspective\CustomerProductInfoGraphQl\Service;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * CurrentCustomer Class.
 */
class CurrentCustomer
{
    protected ?int $customerId = null;

    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @var GroupRepositoryInterface
     */
    protected GroupRepositoryInterface $groupRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * CurrentCustomer constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        GroupRepositoryInterface $groupRepository,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->logger = $logger;
    }

    /**
     * Set customer ID for the current operation to avoid passing it as a parameter.
     *
     * @param $customerId
     * @return void
     */
    public function setCustomerId($customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * Check if customer is authorized.
     *
     * @return bool
     */
    public function isCustomerLoggedIn(): bool
    {
        return (bool)$this->customerId;
    }

    /**
     * Get customer by id.
     *
     * @return CustomerInterface|null
     * @throws LocalizedException
     */
    public function getCustomer(): ?CustomerInterface
    {
        try {
            return $this->customerRepository->getById($this->customerId);
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * Get customer group code (name).
     *
     * @return string
     */
    public function getCustomerGroupName(): string
    {
        try {
            return $this->groupRepository->getById($this->getCustomer()->getGroupId())->getCode();
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
            return '';
        }
    }

    /**
     * Get current customer ID (used externally).
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }
}
