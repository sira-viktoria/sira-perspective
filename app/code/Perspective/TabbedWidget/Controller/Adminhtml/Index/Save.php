<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Perspective\TabbedWidget\Model\Condition as ConditionModel;
use Perspective\TabbedWidget\Model\ResourceModel\Condition as ConditionResourceModel;
use RuntimeException;

class Save extends Action
{
    /**
     * @var ConditionModel
     */
    protected ConditionModel $conditionModel;
    /**
     * @var ConditionResourceModel
     */
    protected ConditionResourceModel $conditionResourceModel;
    /**
     * @var Session
     */
    protected Session $adminSession;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     * @param ConditionModel $conditionModel
     * @param ConditionResourceModel $conditionResourceModel
     * @param Session $adminSession
     */
    public function __construct(
        Action\Context $context,
        ConditionModel $conditionModel,
        ConditionResourceModel $conditionResourceModel,
        Session $adminSession
    ) {
        parent::__construct($context);
        $this->conditionModel = $conditionModel;
        $this->conditionResourceModel = $conditionResourceModel;
        $this->adminSession = $adminSession;
    }

    /**
     * @return ResultInterface|ResponseInterface|Redirect
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $condition_id = $this->getRequest()->getParam('condition_id');
            if ($condition_id) {
                $this->conditionResourceModel->load($this->conditionModel, $condition_id);
            }

            $data['conditions'] = json_encode($data['rule']['conditions']);
            unset($data['rule']);

            $model = $this->conditionModel->setData($data);

            try {
                $this->conditionResourceModel->save($model);
                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    }
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException|RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }
            $this->_getSession()->setFormData($data);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
