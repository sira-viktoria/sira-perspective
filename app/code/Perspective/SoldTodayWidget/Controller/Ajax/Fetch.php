<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Controller\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Result\PageFactory;
use Perspective\SoldTodayWidget\ViewModel\ProductViewModel;

/**
 * Fetch Class.
 */
class Fetch implements HttpGetActionInterface
{
    /**
     * @var RawFactory
     */
    protected RawFactory $resultRawFactory;

    /**
     * @var PageFactory
     */
    protected PageFactory $layoutFactory;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var ProductViewModel
     */
    protected ProductViewModel $viewModel;

    /**
     * Fetch constructor.
     *
     * @param RawFactory $resultRawFactory
     * @param PageFactory $layoutFactory
     * @param RequestInterface $request
     * @param ProductViewModel $viewModel
     */
    public function __construct(
        RawFactory $resultRawFactory,
        PageFactory $layoutFactory,
        RequestInterface $request,
        ProductViewModel $viewModel
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->request = $request;
        $this->viewModel = $viewModel;
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        $layout = $this->layoutFactory->create()->getLayout();
        $currentProductId = $this->request->getParam('current_product_id', '');

        try {
            $html = $layout->createBlock(\Perspective\SoldTodayWidget\Block\SoldProducts::class)
                ->setTemplate('Perspective_SoldTodayWidget::widget/content.phtml')
                ->setCurrentProductId((int)$currentProductId)
                ->setViewModel($this->viewModel)
                ->toHtml();
        } catch (LocalizedException $e) {
            $html = '';
        }

        return $resultRaw->setContents($html);
    }
}
