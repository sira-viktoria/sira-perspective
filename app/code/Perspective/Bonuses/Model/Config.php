<?php
declare(strict_types=1);

namespace Perspective\Bonuses\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryRepository;

/**
 * Config Model.
 */
class Config
{
    /**
     * Paths to Configurations.
     */
    public  const string XML_PATH = 'sales/perspective_bonuses/';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;
    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepository,
        CategoryRepository $categoryRepository,
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function get($path)
    {
        return $this->scopeConfig->getValue(self::XML_PATH . $path);

    }

    /**
     * @param $value
     *
     * @return array
     */
    public function stringToArray($value): array
    {
        return array_map('intval', explode(',', $value));
    }

    /**
     * @param $sku
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCategoryIdsByProductSku($sku): array
    {
        $product = $this->productRepository->get($sku);
        return $product->getCategoryIds();
    }

    /**
     * @param $categoryId
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCategoryNameById($categoryId): string
    {
        return $this->categoryRepository->get($categoryId)->getName();
    }

    /**
     * @param $productId
     *
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getProductById($productId): ProductInterface
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * @param $productId
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductSkuById($productId): string
    {
        return $this->productRepository->getById($productId)->getSku();
    }
}
