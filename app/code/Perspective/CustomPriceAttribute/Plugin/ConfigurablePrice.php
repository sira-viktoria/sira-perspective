<?php
declare(strict_types=1);

namespace Perspective\CustomPriceAttribute\Plugin;

use Magento\Framework\App\Request\Http as HttpRequest;

/**
 * ConfigurablePrice Plugin.
 */
class ConfigurablePrice
{
    /**
     * @var HttpRequest
     */
    protected HttpRequest $request;

    /**
     * ConfigurablePrice constructor.
     *
     * @param HttpRequest $request
     */
    public function __construct(HttpRequest $request) {
        $this->request = $request;
    }

    /**
     * @param $subject
     * @param $result
     * @return float|mixed
     */
    public function afterGetValue($subject, $result): mixed
    {
        $fullActionName = $this->request->getFullActionName();
        if ($fullActionName !== 'catalog_category_view' && $fullActionName !== 'catalogsearch_result_index') {
            return $result;
        }

        $product = $subject->getProduct();
        if (!$product) {
            return $result;
        }

        $customPrice = $product->getData('custom_price');
        if ($customPrice && $customPrice > 0) {
            return (float)$customPrice;
        }

        return $result;
    }
}
