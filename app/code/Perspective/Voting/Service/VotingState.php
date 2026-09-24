<?php
namespace Perspective\Voting\Service;

use Perspective\Voting\Model\Source\ManagementType;
use Perspective\Voting\Model\VotingManager;

/**
 * VotingState Class.
 */
class VotingState
{
    public const string STATE_FINISHED = 'finished';
    public const string STATE_AUTO     = 'auto';
    public const string STATE_MANUAL_ACTIVE   = 'manual_active';
    public const string STATE_MANUAL_INACTIVE = 'manual_inactive';

    /**
     * @var VotingManager
     */
    protected VotingManager $votingManager;

    /**
     * VotingState constructor.
     *
     * @param VotingManager $votingManager
     */
    public function __construct(
        VotingManager $votingManager
    ) {
        $this->votingManager = $votingManager;
    }

    /**
     * @param int $id
     * @return string
     */
    public function getStateByVotingId(int $id): string
    {
        $voting = $this->votingManager->getById($id);
        $managementType = $voting->getManagementType();
        $manualStatus = $voting->getStatus();

        //Check if voting is already finished, manual, or automated by date
        return match (true) {
            (bool)$voting->getIsFinished() => self::STATE_FINISHED,
            $voting->getManagementType() == ManagementType::TYPE_BY_DATE => self::STATE_AUTO,
            $managementType == ManagementType::TYPE_MANUAL && $manualStatus == true => self::STATE_MANUAL_ACTIVE,
            $managementType == ManagementType::TYPE_MANUAL && $manualStatus == false => self::STATE_MANUAL_INACTIVE,
            default => 'undefined'
        };
    }
}
