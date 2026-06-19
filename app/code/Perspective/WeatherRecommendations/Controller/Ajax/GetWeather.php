<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Controller\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Result\PageFactory;
use Perspective\WeatherRecommendations\ViewModel\WeatherViewModel;

/**
 * GetWeather Class.
 */
class GetWeather implements HttpGetActionInterface
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
     * @var WeatherViewModel
     */
    protected WeatherViewModel $viewModel;

    /**
     * Fetch constructor.
     *
     * @param RawFactory $resultRawFactory
     * @param PageFactory $layoutFactory
     * @param RequestInterface $request
     */
    public function __construct(
        RawFactory $resultRawFactory,
        PageFactory $layoutFactory,
        RequestInterface $request,
        WeatherViewModel $viewModel
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
        $resultRaw->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $resultRaw->setHeader('Pragma', 'no-cache', true);

        try {
            $html = $layout->createBlock(\Perspective\WeatherRecommendations\Block\Widget\RecommendationProducts::class)
                ->setTemplate('Perspective_WeatherRecommendations::widget/weather_info.phtml')
                ->setViewModel($this->viewModel)
                ->toHtml();
        } catch (LocalizedException $e) {
            $html = '';
        }

        return $resultRaw->setContents($html);
    }
}
