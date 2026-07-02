<?php
declare(strict_types=1);

namespace Perspective\ProductExtraInfo\Service;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;

/**
 * ProductExtraData Service Class.
 */
class ProductExtraData
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * @var LinkManagementInterface
     */
    protected LinkManagementInterface $configurableChildrenInterface;

    /**
     * ProductExtraData constructor.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     * @param LinkManagementInterface $configurableChildrenInterface
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        LinkManagementInterface $configurableChildrenInterface
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->configurableChildrenInterface = $configurableChildrenInterface;
    }

    /**
     * @param ProductInterface $product
     *
     * @return array
     */
    public function getCategoryNames(ProductInterface $product): array
    {
        $categoryNames = [];
        $categoryIds = $product->getCategoryIds();

        foreach ($categoryIds as $categoryId) {
            try {
                $categoryNames[] = $this->categoryRepository->get($categoryId)->getName();
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }
        return array_unique($categoryNames);
    }

    /**
     * Get min price from simple products
     * @param ProductInterface $product configurable
     *
     * @return string
     */
    public function getMinSimplePrice(ProductInterface $product): string
    {
        $simplePrices = [];
        $simpleProducts = $this->configurableChildrenInterface->getChildren($product->getSku());

        foreach ($simpleProducts as $simpleProduct) {
            $simplePrices[] = $simpleProduct->getPrice();
        }

        if (empty($simplePrices)) {
            return '';
        }
        return min($simplePrices);
    }
}
