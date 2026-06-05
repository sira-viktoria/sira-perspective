<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomCartProductShipping\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;

/**
 * CreatePensionerCustomerGroup Class.
 */
class CreatePensionerCustomerGroup implements DataPatchInterface
{
    const CUSTOMER_PENSIONER_GROUP_CODE = 'Pensioner';
    const DEFAULT_TAX_CLASS_ID = 3;

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
            $group = $this->groupFactory->create();
            $group->setCode(self::CUSTOMER_PENSIONER_GROUP_CODE);
            $group->setTaxClassId(self::DEFAULT_TAX_CLASS_ID);

            $this->groupRepository->save($group);
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
