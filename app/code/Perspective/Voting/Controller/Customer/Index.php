<?php
declare(strict_types=1);

namespace Perspective\Voting\Controller\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Index Class.
 */
class Index implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param PageFactory $resultPageFactory
     */

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var RedirectFactory
     */
    protected RedirectFactory $resultRedirectFactory;

    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * @return Page|ResultInterface|ResponseInterface
     */
    public function execute(): Page|ResultInterface|ResponseInterface
    {
        if (!$this->customerSession->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Voting'));

        return $resultPage;
    }
}
