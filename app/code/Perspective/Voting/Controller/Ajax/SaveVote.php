<?php
declare(strict_types=1);

namespace Perspective\Voting\Controller\Ajax;

use Exception;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\UrlInterface;
use Perspective\Voting\Exception\VotingException;
use Perspective\Voting\Model\VoteManager;
use Perspective\Voting\Model\VotingManager;
use Perspective\Voting\Service\UserIdentification;
use Psr\Log\LoggerInterface;

/**
 * SaveVote Ajax Controller.
 */
class SaveVote implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var VoteManager
     */
    protected VoteManager$voteManager;

    /**
     * @var UserIdentification
     */
    protected UserIdentification $userIdentificationService;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlInterface;

    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;

    /**
     * @var VotingManager
     */
    protected VotingManager $votingManager;

    /**
     * SaveVote constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     * @param VoteManager $voteManager
     * @param UserIdentification $userIdentificationService
     * @param LoggerInterface $logger
     * @param UrlInterface $urlInterface
     * @param CustomerSession $customerSession
     * @param VotingManager $votingManager
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        RequestInterface $request,
        VoteManager $voteManager,
        UserIdentification $userIdentificationService,
        LoggerInterface $logger,
        UrlInterface $urlInterface,
        CustomerSession $customerSession,
        VotingManager $votingManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->voteManager = $voteManager;
        $this->userIdentificationService = $userIdentificationService;
        $this->logger = $logger;
        $this->urlInterface = $urlInterface;
        $this->customerSession = $customerSession;
        $this->votingManager = $votingManager;
    }

    /**
     * Trigger vote processing or redirect to registration if guest voting is restricted
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute(): Json|ResultInterface|ResponseInterface
    {
        $result = $this->resultJsonFactory->create();

        try {
            $params = $this->request->getParams();

            $identity = $this->userIdentificationService->getIdentityData();

            //if no customer id + guest not allow -> redirect to register page
            if (!$identity['customer_id'] && !$this->votingManager->isGuestVotingAllowed((int)$params['voting_id'])) {
                $refererUrl = $this->request->getServer('HTTP_REFERER');
                $this->customerSession->setData('voting_referer', $refererUrl);
                $this->customerSession->setData('temporary_vote', [
                    'voting_id' => $params['voting_id'],
                    'option_id' => $params['option_id']
                ]);

                return $result->setData([
                    'success'  => false,
                    'redirect' => true,
                    'url'      => $this->urlInterface->getUrl('customer/account/login')
                ]);
            }

            $message = $this->voteManager->processVote((int)$params['voting_id'], (int)$params['option_id'], $identity);

            return $result->setData([
                'success' => true,
                'redirect' => false,
                'message' => $message
            ]);
        } catch (VotingException $e) {
            return $result->setData([
                'success' => false,
                'redirect' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $result->setData([
                'success' => false,
                'redirect' => false,
                'message' => __('An error occurred while processing your vote.')
            ]);
        }
    }
}
