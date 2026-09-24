<?php
declare(strict_types=1);

namespace Perspective\Voting\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Perspective\Voting\Model\VotingFactory;
use Perspective\Voting\Model\ResourceModel\Voting as VotingResource;
use Perspective\Voting\Model\VotingManager;

/**
 * Finish Class.
 */
class Finish extends Action
{
    /**
     * @var VotingFactory
     */
    protected VotingFactory $votingFactory;

    /**
     * @var VotingResource
     */
    protected VotingResource $votingResource;

    /**
     * @var VotingManager
     */
    protected VotingManager $votingManager;

    /**
     * VotingManager constructor.
     *
     * @param Context $context
     * @param VotingFactory $votingFactory
     * @param VotingResource $votingResource
     * @param VotingManager $votingManager
     */
    public function __construct(
        Context $context,
        VotingFactory $votingFactory,
        VotingResource $votingResource,
        VotingManager $votingManager,
    ) {
        $this->votingFactory = $votingFactory;
        $this->votingResource = $votingResource;
        $this->votingManager = $votingManager;
        parent::__construct($context);
    }

    /**
     * Trigger voting finish process and redirect to edit form
     *
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws AlreadyExistsException
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $votingId = (int)$this->getRequest()->getParam('voting_id');

        $this->votingManager->finishVoting($votingId);

        $this->messageManager->addSuccessMessage(__('Voting finished.'));

        return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['voting_id' => $votingId]);
    }
}
