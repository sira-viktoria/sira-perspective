<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomCartProductShipping\Model\Service;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Session;
use Perspective\CustomCartProductShipping\Model\Config;

/**
 * IsAvailableCustomerByGroup Service.
 */
class IsAvailableCustomerByGroup
{
    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var GroupRepositoryInterface
     */
    protected GroupRepositoryInterface$groupRepository;

    /**
     * GetCustomerCurrentGroup constructor.
     *
     * @param Session $customerSession
     * @param Config $config
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        Session $customerSession,
        Config $config,
        GroupRepositoryInterface $groupRepository,
    ) {
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @return bool
     */
    public function isAvailableCustomerByGroup(): bool
    {
        $availableCustomerGroups = $this->config->getAvailableCustomerGroupsId();
        $currentCustomerGroup = $this->getCustomerGroupId();

        return in_array($currentCustomerGroup, $availableCustomerGroups);
    }

    /**
     * @return int
     */
    public function getCustomerGroupId(): int
    {
        if ($this->customerSession->isLoggedIn()) {
            return (int)$this->customerSession->getCustomer()->getGroupId();
        }

        return 0;
    }
}
