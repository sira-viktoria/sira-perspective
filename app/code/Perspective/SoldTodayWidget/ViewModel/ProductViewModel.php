<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\ViewModel;

use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\LayoutInterface;

/**
 * ProductViewModel.
 */
class ProductViewModel implements ArgumentInterface
{
    /**
     * @var ImageHelper
     */
    private ImageHelper $imageHelper;

    /**
     * @var LayoutInterface
     */
    protected LayoutInterface $layout;

    /**
     * ProductViewModel constructor.
     *
     * @param ImageHelper $imageHelper
     * @param LayoutInterface $layout
     */
    public function __construct(
        ImageHelper $imageHelper,
        LayoutInterface $layout,
    ) {
        $this->imageHelper = $imageHelper;
        $this->layout = $layout;
    }

    /**
     * Return HTML block with price
     *
     * @param Product $product
     * @return string
     * @throws LocalizedException
     */
    public function getProductPrice(Product $product): string
    {
        return $this->getProductPriceHtml(
            $product,
            FinalPrice::PRICE_CODE,
            Render::ZONE_ITEM_LIST
        );
    }


    /**
     * @param Product $product
     * @param $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        Product $product,
                $priceType,
        string  $renderZone = Render::ZONE_ITEM_LIST,
        array   $arguments = []
    ): string
    {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var Render $priceRender */
        $priceRender = $this->layout->getBlock('product.price.render.default');
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }
        return $price;
    }

    /**
     * @param Product $product
     * @param string $imageId Наприклад: 'category_page_grid', 'product_page_image_medium'
     * @return ImageHelper
     */
    public function getImage(Product $product, string $imageId): ImageHelper
    {
        return $this->imageHelper->init($product, $imageId);
    }
}
