<?php
declare(strict_types=1);

namespace Perspective\Voting\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\Voting\Service\ActiveWinners;

/**
 * WinnerLabel ViewModel.
 */
class WinnerLabel implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var ActiveWinners
     */
    protected ActiveWinners $activeWinnersService;

    /**
     * WinnerLabel constructor.
     *
     * @param RequestInterface $request
     * @param ActiveWinners $activeWinnersService
     */
    public function __construct(
        RequestInterface $request,
        ActiveWinners $activeWinnersService
    ) {
        $this->request = $request;
        $this->activeWinnersService = $activeWinnersService;
    }

    /**
     * @return string
     */
    public function getWinnerHtml(): string
    {
        $currentProductId = (int)$this->request->getParam('id');

        return $this->activeWinnersService->getWinnerLabelHtml((int)$currentProductId);
    }
}
