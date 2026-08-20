<?php
declare(strict_types=1);

namespace Perspective\Memes\Plugin;

use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Quote\Model\Quote;
use Perspective\Memes\Model\Memes\MemeManager;

class ChangeQuote
{
    /**
     * @var MemeManager
     */
    protected MemeManager $memeManager;

    /**
     * ChangeQuote constructor.
     *
     * @param MemeManager $memeManager
     */
    public function __construct(
        MemeManager $memeManager
    ) {
        $this->memeManager = $memeManager;
    }

    /**
     * @param Quote $subject
     * @param $result
     * @param $product
     * @param $request
     * @param string $processMode
     *
     * @return mixed
     */
    public function afterAddProduct(
        Quote $subject,
        $result,
        $product,
        $request = null,
        string $processMode = AbstractType::PROCESS_MODE_FULL
    ): mixed
    {
        if (is_object($result)) {
            if ($quoteId = (int)$result->getQuoteId()) {
                $this->memeManager->updateData($quoteId, 'quote', 'add');
            }
        }

        return $result;
    }

    /**
     * @param Quote $subject
     * @param $result
     * @param $itemId
     *
     * @return mixed
     */
    public function afterRemoveItem(Quote $subject, $result, $itemId): mixed
    {
        if (is_object($result)) {
            $quoteId = (int)$result->getEntityId();
            $this->memeManager->updateData($quoteId, 'quote', 'remove');
        }

        return $result;
    }
}
