<?php
declare(strict_types=1);

namespace Perspective\Memes\Controller\Ajax;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Perspective\Memes\Model\Memes\MemeManager;
use Psr\Log\LoggerInterface;

/**
 * UpdateSelectedMeme Ajax Controller.
 */
class UpdateSelectedMeme implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var MemeManager
     */
    protected MemeManager $memeManager;

    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    protected MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdInterface;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * UpdateSelectedMeme constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     * @param MemeManager $memeManager
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdInterface
     * @param LoggerInterface $logger
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        RequestInterface $request,
        MemeManager $memeManager,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdInterface,
        LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->memeManager = $memeManager;
        $this->maskedQuoteIdInterface = $maskedQuoteIdInterface;
        $this->logger = $logger;
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $maskedQuoteId = $this->request->getParam('maskedQuoteId');
        $entityType = $this->request->getParam('entityType');
        $selected = $this->request->getParam('selected');

        $result = $this->resultJsonFactory->create();

        try {
            if (ctype_digit($maskedQuoteId)) {
                $quoteId = (int)$maskedQuoteId;
            } else {
                $quoteId = $this->maskedQuoteIdInterface->execute($maskedQuoteId);
            }

            $this->memeManager->updateSelected((int)$quoteId, $entityType, $selected);

            return $result->setData([
                'success' => true,
                'selected' => $selected,
            ]);
        } catch (NoSuchEntityException $e) {
            $this->logger->error(__('Selected meme update failed. %1', $e->getMessage()));
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
