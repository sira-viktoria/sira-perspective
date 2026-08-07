<?php
declare(strict_types=1);

namespace Perspective\CustomPriceAttribute\Model\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Perspective\CustomPriceAttribute\Service\DefaultCustomPrice as DefaultCustomPriceService;

/**
 * Backend model for the custom_price attribute.
 */
class CustomPrice extends AbstractBackend
{
    /**
     * @var DefaultCustomPriceService
     */
    protected DefaultCustomPriceService $defaultCustomPriceService;

    /**
     * CustomPrice constructor.
     *
     * @param DefaultCustomPriceService $defaultCustomPriceService
     */
    public function __construct(
        DefaultCustomPriceService $defaultCustomPriceService,
    ) {
        $this->defaultCustomPriceService = $defaultCustomPriceService;
    }

    /**
     * @param $object
     * @return void
     */
    public function beforeSave($object): void
    {
        $allowModify = $object->getData('use_config_custom_price');

        if ($allowModify !== null) {
            $defaultCustomPrice = $this->defaultCustomPriceService->getDefaultCustomPrice($object);

            if ($defaultCustomPrice == 0 || $defaultCustomPrice === 0.00) {
                $object->unsetData('custom_price');
            } else {
                $object->setData('custom_price', $defaultCustomPrice);
            }
        }
    }
}
