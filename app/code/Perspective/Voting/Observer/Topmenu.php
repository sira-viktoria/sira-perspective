<?php
declare(strict_types=1);

namespace Perspective\Voting\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;
use Perspective\Voting\Service\ConfigData;

/**
 * Topmenu Observer.
 */
class Topmenu implements ObserverInterface
{
    /**
     * @var ConfigData
     */
    protected ConfigData $configDataService;

    /**
     * Topmenu constructor.
     *
     * @param ConfigData $configDataService
     */
    public function __construct(
        ConfigData $configDataService
    ) {
        $this->configDataService = $configDataService;
    }

    /**
     * Add the Voting link to the top navigation menu if enabled in config
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer): static
    {
        if ($this->configDataService->isShowVotingLinkInMenu() &&
            $this->configDataService->isModuleEnabled()
        ) {
            $menu = $observer->getMenu();
            $tree = $menu->getTree();
            $data = [
                'name'      => __('Voting'),
                'id'        => 'voting_menu_item',
                'url'       => '/perspective_voting/index/index',
                'is_active' => false
            ];
            $node = new Node($data, 'id', $tree, $menu);
            $menu->addChild($node);
        }
        return $this;
    }
}
