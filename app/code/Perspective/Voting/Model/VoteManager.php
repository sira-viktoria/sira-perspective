<?php
declare(strict_types=1);

namespace Perspective\Voting\Model;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Phrase;
use Perspective\Voting\Exception\VotingException;
use Perspective\Voting\Model\ResourceModel\VotingVote as VoteResourceModel;
use Perspective\Voting\Model\ResourceModel\VotingVote\CollectionFactory as VoteCollectionFactory;
use Perspective\Voting\Service\VotingValidation;
use Psr\Log\LoggerInterface;

/**
 * VoteManager Class.
 */
class VoteManager
{
    /**
     * @var VoteCollectionFactory
     */
    protected VoteCollectionFactory $voteCollectionFactory;

    /**
     * @var VoteResourceModel
     */
    protected VoteResourceModel $voteResourceModel;

    /**
     * @var VotingManager
     */
    protected VotingManager $votingManager;

    /**
     * @var VotingValidation
     */
    protected VotingValidation $votingValidationService;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;


    /**
     * VoteManager constructor.
     *
     * @param VoteCollectionFactory $voteCollectionFactory
     * @param VoteResourceModel $voteResourceModel
     * @param VotingManager $votingManager
     * @param VotingValidation $votingValidationService
     * @param LoggerInterface $logger
     */
    public function __construct(
        VoteCollectionFactory $voteCollectionFactory,
        VoteResourceModel $voteResourceModel,
        VotingManager $votingManager,
        VotingValidation $votingValidationService,
        LoggerInterface $logger
    ) {
        $this->voteCollectionFactory = $voteCollectionFactory;
        $this->voteResourceModel = $voteResourceModel;
        $this->votingManager = $votingManager;
        $this->votingValidationService = $votingValidationService;
        $this->logger = $logger;
    }

    /**
     * Get vote by user(id/hash) and votingId
     *
     * @param int $votingId
     * @param $customerId
     * @param $guestHash
     * @return DataObject
     */
    public function getUserVoteByVoting(int $votingId, $customerId, $guestHash = null): DataObject
    {
        $collection = $this->voteCollectionFactory->create();
        $collection->addFieldToFilter('voting_id', $votingId);

        if ($customerId) {
            $collection->addFieldToFilter('customer_id', $customerId);
        } else {
            $collection->addFieldToFilter('guest_hash', $guestHash);
        }

        return $collection->getFirstItem();
    }

    /**
     * Get all voting IDs where this customer has voted
     *
     * @param $customerId
     * @return array
     */
    public function getVotingIdsByUserId($customerId): array
    {
        $collection = $this->voteCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        return array_unique($collection->getColumnValues('voting_id'));
    }

    /**
     * Save or update a vote with validation
     *
     * @param int $votingId
     * @param int $optionId
     * @param array $identity
     *
     * @return Phrase
     * @throws AlreadyExistsException
     * @throws VotingException
     */
    public function processVote(int $votingId, int $optionId, array $identity): Phrase
    {
        // voting validation
        $voting = $this->votingManager->getById($votingId);
        $this->votingValidationService->canVote($voting, $identity);

        // user data
        $customerId = $identity['customer_id'];
        $guestHash = $identity['guest_hash'];

        // load/create vote
        $vote = $this->getUserVoteByVoting($votingId, $customerId, $guestHash);
        $isNewVote = !$vote->getId();

        // save/update vote
        $vote->setVotingId($votingId);
        $vote->setOptionId($optionId);
        if ($customerId) {
            $vote->setCustomerId($customerId);
        } else {
            $vote->setGuestHash($guestHash);
        }
        $this->voteResourceModel->save($vote);

        // success msg
        if ($isNewVote) {
            $message = __('Your vote has been successfully counted. Statistics will be updated soon.');
        } else {
            $message = __('Your vote has been successfully updated. Statistics will be updated soon.');
        }
        return $message;
    }

    /**
     * Move guest votes to a customer account
     *
     * @param $guestHash
     * @param $customerId
     * @return void
     * @throws AlreadyExistsException
     */
    public function convertGuestVotesToCustomer($guestHash, $customerId): void
    {
        $guestVoteCollection = $this->voteCollectionFactory->create();
        $guestVoteCollection->addFieldToFilter('guest_hash', $guestHash);

        foreach ($guestVoteCollection as $vote) {
            try {
                $votingId = $vote->getVotingId();

                if ($this->getUserVoteByVoting($votingId, $customerId)->getId()) {
                    // delete guest vote if a customer vote already exists
                    $this->voteResourceModel->delete($vote);
                } else {
                    $vote->setCustomerId($customerId);
                    $vote->setGuestHash(null);
                    $this->voteResourceModel->save($vote);
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
