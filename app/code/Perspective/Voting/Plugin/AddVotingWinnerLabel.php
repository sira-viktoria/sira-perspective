<?php
declare(strict_types=1);

namespace Perspective\Voting\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\AbstractProduct;
use Perspective\Voting\Service\ActiveWinners;
use Perspective\Voting\Service\ConfigData;

/**
 * AddVotingWinnerLabel Plugin.
 */
class AddVotingWinnerLabel
{
    /**
     * @var ActiveWinners
     */
    protected ActiveWinners $activeWinnersService;

    /**
     * @var ConfigData
     */
    protected ConfigData $configDataService;

    /**
     * AddVotingWinnerLabel constructor.
     *
     * @param ActiveWinners $activeWinnersService
     * @param ConfigData $configDataService
     */
    public function __construct(
        ActiveWinners $activeWinnersService,
        ConfigData $configDataService
    ) {
        $this->activeWinnersService = $activeWinnersService;
        $this->configDataService = $configDataService;
    }

    /**
     * @param AbstractProduct $subject
     * @param string $result
     * @param Product $product
     * @return string
     */
    public function afterGetProductDetailsHtml(AbstractProduct $subject, string $result, Product $product): string
    {
        $customHtml = '';
        if ($this->configDataService->isModuleEnabled()) {
            $customHtml = $this->activeWinnersService->getWinnerLabelHtml((int)$product->getId());
        }
        return $result . $customHtml;
    }
}
