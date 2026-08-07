<?php
declare(strict_types=1);

namespace Perspective\CustomPriceAttribute\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Field;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Ui\Component\Form;
use Perspective\CustomPriceAttribute\Service\DefaultCustomPrice as DefaultCustomPriceService;

/**
 * CustomPrice UI Provider.
 */
class CustomPrice extends AbstractModifier
{
    const string FIELD_CUSTOM_PRICE = 'custom_price';
    const string FIELD_USE_CONFIG = 'use_config_custom_price';
    const string DATA_SCOPE_PRODUCT = 'data.product';
    const string CONTAINER_PREFIX = 'container_';

    /**
     * @var ArrayManager
     */
    protected ArrayManager $arrayManager;

    /**
     * @var LocatorInterface
     */
    protected LocatorInterface $locator;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var DefaultCustomPriceService
     */
    protected DefaultCustomPriceService $defaultCustomPriceService;

    /**
     * CustomPrice constructor.
     *
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param ScopeConfigInterface $scopeConfig
     * @param DefaultCustomPriceService $defaultCustomPriceService
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        ScopeConfigInterface $scopeConfig,
        DefaultCustomPriceService $defaultCustomPriceService
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeConfig = $scopeConfig;
        $this->defaultCustomPriceService = $defaultCustomPriceService;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta): array
    {
        return $this->customizeCustomPriceFields($meta);
    }

    /**
     * Configure custom price field.
     *
     * @param array $meta
     * @return array
     */
    protected function customizeCustomPriceFields(array $meta): array
    {
        $pricePath = $this->arrayManager->findPath(
            self::FIELD_CUSTOM_PRICE, $meta, null, 'children');
        if (!$pricePath) {
            return $meta;
        }

        $containerPath = $this->arrayManager->findPath(
            self::CONTAINER_PREFIX . self::FIELD_CUSTOM_PRICE, $meta, null, 'children');

        $meta = $this->arrayManager->merge(
            $pricePath . '/arguments/data/config',
            $meta,
            [
                'additionalClasses' => 'admin__field-small',
                'validation' => [
                    'validate-zero-or-greater' => true
                ],
                'imports' => [
                    'disabled' => '!${$.parentName}.' . self::FIELD_USE_CONFIG . ':checked',
                    '__disableTmpl' => ['disabled' => false],
                ],
            ]
        );

        if ($containerPath) {
            $meta = $this->arrayManager->merge(
                $containerPath . '/arguments/data/config',
                $meta,
                [
                    'component' => 'Magento_Ui/js/form/components/group',
                    'label' => false,
                    'required' => false
                ]
            );
        }

        if ($containerPath) {
            $product = $this->locator->getProduct();

            $isEditable = !$this->defaultCustomPriceService->isDefaultCustomPrice($product);

            $meta = $this->arrayManager->set(
                $pricePath . '/arguments/data/config/disabled',
                $meta,
                !$isEditable
            );

            $fullFieldStructure = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => 'boolean',
                            'formElement' => Form\Element\Checkbox::NAME,
                            'componentType' => Field::NAME,
                            'component' => 'Magento_Ui/js/form/element/single-checkbox',
                            'prefer' => 'checkbox',
                            'dataScope' => self::FIELD_USE_CONFIG,
                            'valueMap' => [
                                'true' => '1',
                                'false' => '0',
                            ],
                            'label' => __('Allow Modify'),
                            'description' => __('Allow Modify'),
                            'sortOrder' => 10,
                            'value' => $isEditable ? '1' : '0',
                            'checked' => $isEditable
                        ]
                    ]
                ]
            ];

            $meta = $this->arrayManager->set(
                $containerPath . '/children/' . self::FIELD_USE_CONFIG,
                $meta,
                $fullFieldStructure
            );
        }
        return $meta;
    }
}
