<?php
declare(strict_types=1);

namespace Perspective\Voting\Cron;

use Magento\Framework\Exception\AlreadyExistsException;
use Perspective\Voting\Model\ResourceModel\Voting\CollectionFactory;
use Perspective\Voting\Service\VoteCalculator;
use Perspective\Voting\Model\VotingOptionManager;
use Perspective\Voting\Service\ConfigData;
use Perspective\Voting\Service\CacheManager;
use Psr\Log\LoggerInterface;
use Perspective\Voting\Model\VotingManager;

/**
 * RefreshVotes Cron.
 */
class RefreshVotes
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var VoteCalculator
     */
    protected VoteCalculator $voteCalculator;

    /**
     * @var VotingOptionManager
     */
    protected VotingOptionManager $optionManager;

    /**
     * @var ConfigData
     */
    protected ConfigData $configDataService;

    /**
     * @var CacheManager
     */
    protected CacheManager $cacheManager;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var VotingManager
     */
    protected VotingManager $votingManager;

    /**
     * RefreshVotes constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param VoteCalculator $voteCalculator
     * @param VotingOptionManager $optionManager
     * @param ConfigData $configDataService
     * @param CacheManager $cacheManager
     * @param LoggerInterface $logger
     * @param VotingManager $votingManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        VoteCalculator $voteCalculator,
        VotingOptionManager $optionManager,
        ConfigData $configDataService,
        CacheManager $cacheManager,
        LoggerInterface $logger,
        VotingManager $votingManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->voteCalculator = $voteCalculator;
        $this->optionManager = $optionManager;
        $this->configDataService = $configDataService;
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
        $this->votingManager = $votingManager;
    }

    /**
     * Recalculate vote statistics and refresh cache for all active voting.
     *
     * @return void
     * @throws AlreadyExistsException
     */
    public function execute(): void
    {
        if (!$this->configDataService->isModuleEnabled()) {
            return;
        }

        $allIds = $this->votingManager->getActiveVotingIds();

        if (!empty($allIds)) {
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('voting_id', ['in' => $allIds]);

            foreach ($collection as $voting) {
                $votingId = (int)$voting->getId();
                $finalVotes = $this->voteCalculator->getFinalVotesByVotingId($votingId);
                $this->optionManager->updateVotes($finalVotes);
                $this->cacheManager->deleteVotingCache($votingId);
            }
        }
        $this->logger->info(__('Votes refreshed for %1 voting', count($allIds)));
    }
}
