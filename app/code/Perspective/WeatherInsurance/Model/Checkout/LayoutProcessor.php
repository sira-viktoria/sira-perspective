<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Model\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * LayoutProcessor Class.`
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        // Шлях до платіжних методів у стандартному Checkout
        $paymentPath = ['components', 'checkout', 'children', 'steps', 'children', 'billing-step', 'children', 'payment', 'children'];

        // 1. Спробуємо знайти через стандартний paymentsList
//        $listPath = array_merge($paymentPath, ['payments-list', 'children']);
//        $this->injectIntoBeforePlaceOrder($jsLayout, $listPath);

        // 2. Спробуємо знайти через renders (інші платіжні шлюзи)
//        $rendersPath = array_merge($paymentPath, ['renders', 'children']);
//        if ($this->arrayHasPath($jsLayout, $rendersPath)) {
//            $groups = $this->getNestedArrayValue($jsLayout, $rendersPath);
//            foreach ($groups as $groupKey => $groupValue) {
//                if (isset($groupValue['children'])) {
//                    foreach ($groupValue['children'] as $methodKey => $methodValue) {
//                        $specificPath = array_merge($rendersPath, [$groupKey, 'children', $methodKey]);
//                        $this->injectIntoBeforePlaceOrder($jsLayout, $specificPath);
//                    }
//                }
//            }
//        }

        // 3. Універсальний резервний варіант для One Step Checkout модулів
        // Якщо тема використовує спрощений контейнер "before-place-order" прямо в payment
//        $directPath = array_merge($paymentPath, ['before-place-order', 'children']);
//        if ($this->arrayHasPath($jsLayout, $directPath)) {
//            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['before-place-order']['children']['weather-insurance-checkbox'] = [
//                'component' => 'Perspective_WeatherInsurance/js/view/weather-insurance-checkbox',
//                'displayArea' => 'before-place-order',
//                'dataScope' => 'weather-insurance-checkbox',
//                'provider' => 'checkoutProvider',
//                'sortOrder' => 40
//            ];
//        }

        return $jsLayout;
    }

    /**
     * Впровадження компонента в знайдену гілку before-place-order
     */
    private function injectIntoBeforePlaceOrder(&$jsLayout, $basePath)
    {
        $methods = $this->getNestedArrayValue($jsLayout, $basePath);
        if (is_array($methods)) {
            foreach ($methods as $methodKey => $methodValue) {
                // Перевіряємо наявність або створюємо контейнер before-place-order
                $targetPath = array_merge($basePath, [$methodKey, 'children', 'before-place-order', 'children']);
//                $targetPath = array_merge($basePath, [$methodKey, 'children']);
                $this->setNestedArrayValue($jsLayout, $targetPath, 'weather-insurance-checkbox', [
                    'component' => 'Perspective_WeatherInsurance/js/view/weather-insurance-checkbox',
//                    'template' => 'Perspective_WeatherInsurance/view/weather-insurance-checkbox',
                    'displayArea' => 'before-place-order',
                    'dataScope' => 'weather-insurance-checkbox',
                    'provider' => 'checkoutProvider',
                    'sortOrder' => 40
                ]);
            }
        }
    }

    private function arrayHasPath($array, $path) {
        foreach ($path as $key) {
            if (!is_array($array) || !isset($array[$key])) return false;
            $array = $array[$key];
        }
        return true;
    }

    private function getNestedArrayValue($array, $path) {
        foreach ($path as $key) {
            if (isset($array[$key])) $array = $array[$key];
            else return null;
        }
        return $array;
    }

    private function setNestedArrayValue(&$array, $path, $field, $value) {
        $temp = &$array;
        foreach ($path as $key) {
            if (!isset($temp[$key]) || !is_array($temp[$key])) {
                $temp[$key] = [];
            }
            $temp = &$temp[$key];
        }
        $temp[$field] = $value;
    }
}
