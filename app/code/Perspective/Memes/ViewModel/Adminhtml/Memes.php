<?php
namespace Perspective\Memes\ViewModel\Adminhtml;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\Memes\Model\Memes\MemeManager;
use Magento\Backend\Model\Session\Quote;

class Memes implements ArgumentInterface
{
    /**
     * @var MemeManager
     */
    protected MemeManager $memeManager;
    /**
     * @var Quote
     */
    protected Quote $adminQuoteSession;

    /**
     * @param MemeManager $memeManager
     * @param Quote $adminQuoteSession
     */
    public function __construct(
        MemeManager $memeManager,
        Quote $adminQuoteSession,
    ) {
        $this->memeManager = $memeManager;
        $this->adminQuoteSession = $adminQuoteSession;
    }

    /**
     * Memes constructor.
     *
     * @return string
     */
    public function getJsonMemesData(): string
    {
        $entityId = $this->getQuoteId();
        return json_encode($this->memeManager->getData($entityId, 'quote'));
    }

    /**
     * @return int
     */
    public function getQuoteId(): int
    {
        return $this->adminQuoteSession->getQuoteId();
    }
}
