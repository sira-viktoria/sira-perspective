<?php
declare(strict_types=1);

namespace Perspective\Voting\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Perspective\Voting\Service\UserIdentification;
use Perspective\Voting\Model\ResourceModel\VotingVote\CollectionFactory;
use Random\RandomException;

/**
 * VotingData Class.
 */
class VotingData implements SectionSourceInterface
{
    /**
     * @var UserIdentification
     */
    protected UserIdentification $userIdentification;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $voteCollectionFactory;

    /**
     * VotingData constructor.
     *
     * @param UserIdentification $userIdentification
     * @param CollectionFactory $voteCollectionFactory
     */
    public function __construct(
        UserIdentification $userIdentification,
        CollectionFactory $voteCollectionFactory
    ) {
        $this->userIdentification = $userIdentification;
        $this->voteCollectionFactory = $voteCollectionFactory;
    }


    /**
     * Get customer or guest voting history(private section data) for UI pre-selection.
     *
     * @return array[]
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws RandomException
     */
    public function getSectionData(): array
    {
        $identity = $this->userIdentification->initIdentityData();
        $customerId = $identity['customer_id'];
        $guestHash = $identity['guest_hash'];

        $collection = $this->voteCollectionFactory->create();

        if ($customerId) {
            $collection->addFieldToFilter('customer_id', $customerId);
        } else {
            $collection->addFieldToFilter('guest_hash', $guestHash);
        }

        $votes = [];
        foreach ($collection as $vote) {
            $votes[$vote->getVotingId()] = (int)$vote->getOptionId();
        }

        return [
            'votes' => $votes
        ];
    }
}
