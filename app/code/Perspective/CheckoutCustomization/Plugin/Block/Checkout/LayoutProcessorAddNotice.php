<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\CheckoutCustomization\Plugin\Block\Checkout;

/**
 * LayoutProcessorAddNotice Plugin.
 */
class LayoutProcessorAddNotice
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']['notice'] = __('You can not change your name once account is created.');
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['lastname']['notice'] = __(' Your comment');
        return $jsLayout;
    }
}
