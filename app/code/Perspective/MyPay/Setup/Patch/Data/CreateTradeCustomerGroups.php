<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\MyPay\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;

/**
 * CreateTradeCustomerGroups Class.
 */
class CreateTradeCustomerGroups implements DataPatchInterface
{
    const string CUSTOMER_TRADE_GROUP_CODE = 'Trade';
    const string CUSTOMER_LARGE_TRADE_GROUP_CODE = 'Large Trade';
    const int DEFAULT_TAX_CLASS_ID = 3;

    protected array $customerGroupArray = [
        self::CUSTOMER_TRADE_GROUP_CODE,
        self::CUSTOMER_LARGE_TRADE_GROUP_CODE,
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var GroupInterfaceFactory
     */
    private GroupInterfaceFactory $groupFactory;

    /**
     * @var GroupRepositoryInterface
     */
    private GroupRepositoryInterface $groupRepository;

    /**
     *  CreatePensionerCustomerGroup constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param GroupInterfaceFactory $groupFactory
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        GroupInterfaceFactory $groupFactory,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->groupFactory = $groupFactory;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        try {
            foreach ($this->customerGroupArray as $customerGroupCode) {
                $group = $this->groupFactory->create();
                $group->setCode($customerGroupCode);
                $group->setTaxClassId(self::DEFAULT_TAX_CLASS_ID);

                $this->groupRepository->save($group);
            }
        } catch (\Exception $e) {
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
