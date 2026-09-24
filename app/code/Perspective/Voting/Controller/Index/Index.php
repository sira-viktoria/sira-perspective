<?php
declare(strict_types=1);

namespace Perspective\Voting\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Perspective\Voting\Service\ConfigData;

/**
 * Index Controller.
 */
class Index implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var ConfigData
     */
    protected ConfigData $configDataService;

    /**
     * @var RedirectFactory
     */
    protected RedirectFactory $resultRedirectFactory;

    /**
     * Index constructor.
     *
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        PageFactory $resultPageFactory,
        ConfigData $configDataService,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->configDataService = $configDataService;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute(): Page|ResultInterface|ResponseInterface
    {
        if ($this->configDataService->isModuleEnabled()
        ) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Active Voting'));

            return $resultPage;
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('/');
        return $resultRedirect;
    }
}
