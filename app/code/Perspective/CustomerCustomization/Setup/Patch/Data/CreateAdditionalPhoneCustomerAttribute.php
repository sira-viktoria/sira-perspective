<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CustomerCustomization\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * CreateAdditionalPhoneCustomerAttribute Class.
 */
class CreateAdditionalPhoneCustomerAttribute implements DataPatchInterface
{
    const string CUSTOMER_ADDITIONAL_PHONE_ATTRIBUTE_CODE = 'additional_phone';

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     *  CreatePensionerCustomerGroup constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->addAttribute(Customer::ENTITY, self::CUSTOMER_ADDITIONAL_PHONE_ATTRIBUTE_CODE, [
            'type' => 'varchar',
            'label' => 'Additional Phone',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => 1,
            'is_user_defined' => 1,
            'sort_order' => 100,
            'position' => 100,
            'system' => 0,
            'is_system' => 0,
        ]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $customerSetup->addAttributeToSet(
            Customer::ENTITY,
            $attributeSetId,
            'General',
            self::CUSTOMER_ADDITIONAL_PHONE_ATTRIBUTE_CODE,
            100 // Sort order
        );


        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, self::CUSTOMER_ADDITIONAL_PHONE_ATTRIBUTE_CODE);

        $attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);

        $attribute->save();
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
