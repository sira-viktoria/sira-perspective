<?php
declare(strict_types=1);

namespace Perspective\Memes\Service;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Perspective\Memes\Model\Memes\MemeDataHandler;

/**
 * MemeSearchWord Service.
 */
class MemeSearchWord
{
    protected string $currentSearchWord = 'test';
    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * @var MemeDataHandler
     */
    protected MemeDataHandler $memeDataHandler;

    protected StoreManagerInterface $storeManager;

    /**
     * MemeSearchWord constructor.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     * @param MemeDataHandler $memeDataHandler
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        MemeDataHandler $memeDataHandler,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->memeDataHandler = $memeDataHandler;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns a search word for Giphy API based on quote subtotal:
     * if subtotal < 100 returns 'Test', else first word of first product's first category
     *
     * @param int $quoteId
     * @param string $action
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSearchWordForQuote(int $quoteId, string $action = ''): string
    {
        $quote = $this->memeDataHandler->getEntity('quote', $quoteId);

        if ($quote->getAllItems() && $item = array_last($quote->getAllItems())) {
            $categoryIds = $item->getProduct()->getCategoryIds();
            $rootCategoryId = $this->getCurrentRootCategoryId();
            $categoryId = array_first($categoryIds);
            if ($rootCategoryId == $categoryId) {
                $categoryId = array_last($categoryIds);
            }

            if ($action == 'delete') {
                $categoryId = array_last($categoryIds);
            }

            $category = $this->categoryRepository->get($categoryId);
            $categoryName = $category->getName();
            $words = explode(' ', $categoryName);
            $this->currentSearchWord = $words[0];
        }

        return $this->currentSearchWord;
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentRootCategoryId(): int
    {
        return (int)$this->storeManager->getStore()->getRootCategoryId();
    }
}
