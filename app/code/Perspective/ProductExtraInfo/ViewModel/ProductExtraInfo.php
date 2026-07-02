<?php
declare(strict_types=1);

namespace Perspective\ProductExtraInfo\ViewModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Block\Product\View;
use Perspective\ProductExtraInfo\Service\ProductExtraData;
use Magento\Store\Model\StoreManagerInterface;

/**
 * ProductExtraInfo ViewModel.
 */
class ProductExtraInfo implements ArgumentInterface
{
    protected ?ProductInterface $currentProduct = null;

    protected ?bool $isConfigurable = null;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var View
     */
    protected View $productViewBlock;

    /**
     * @var ProductExtraData
     */
    protected ProductExtraData $productExtraData;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * ProductExtraInfo constructor.
     *
     * @param View $productViewBlock
     * @param ProductExtraData $productExtraData
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        View $productViewBlock,
        ProductExtraData $productExtraData,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productViewBlock = $productViewBlock;
        $this->productExtraData = $productExtraData;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @return ProductInterface|null
     *
     * @throws NoSuchEntityException
     */
    protected function getCurrentProduct(): ?ProductInterface
    {
        if ($this->currentProduct === null) {
            $id = $this->productViewBlock->getProduct()->getId();
            $this->currentProduct = $this->productRepository->getById($id);
        }
        return $this->currentProduct;
    }

    /**
     * @return array
     *
     * @throws NoSuchEntityException
     */
    public function getCategoryNames(): array
    {
        return $this->productExtraData->getCategoryNames($this->getCurrentProduct());
    }

    /**
     * @return bool
     *
     * @throws NoSuchEntityException
     */
    public function isConfigurable(): bool
    {
        if ($this->isConfigurable === null) {
            $this->isConfigurable = $this->getCurrentProduct()->getTypeId() == 'configurable';
        }
        return $this->isConfigurable;
    }

    /**
     * @return string
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getFormattedMinPrice(): string
    {
        if (!$this->isConfigurable()) {
            return '';
        }

        $minPrice = $this->productExtraData->getMinSimplePrice($this->getCurrentProduct());
        $currency = $this->storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();

        return sprintf('%s%.2f', $currency, $minPrice);
    }
}
