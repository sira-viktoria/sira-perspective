<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Block\Adminhtml\Order\Totals;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * WeatherInsurance Class.
 */
class WeatherInsurance extends AbstractBlock
{
    /**
     * Initialize order totals.
     *
     * @return $this
     */
    public function initTotals(): static
    {
        $parent = $this->getParentBlock();
        if (!$parent) {
            return $this;
        }

        $order = $parent->getOrder();

        $insureAmount = $order->getData('weather_insurance');

        if ($insureAmount > 0) {
            $total = new DataObject([
                'code' => 'perspective_weather_insurance_total',
                'value' => $insureAmount,
                'base_value' => $insureAmount,
                'label' => __('Weather Insurance'),
                'area' => ''
            ]);

            $parent->addTotalBefore($total, 'grand_total');
        }

        return $this;
    }
}
