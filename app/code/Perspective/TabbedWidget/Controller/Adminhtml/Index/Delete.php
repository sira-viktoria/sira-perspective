<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Perspective\TabbedWidget\Model\Condition as ConditionModel;
use Perspective\TabbedWidget\Model\ResourceModel\Condition as ConditionResourceModel;

class Delete extends Action
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
     * Delete constructor.
     *
     * @param Context $context
     * @param ConditionModel $conditionModel
     * @param ConditionResourceModel $conditionResourceModel
     */
    public function __construct(
        Context $context,
        ConditionModel $conditionModel,
        ConditionResourceModel $conditionResourceModel
    ) {
        parent::__construct($context);
        $this->conditionModel = $conditionModel;
        $this->conditionResourceModel = $conditionResourceModel;
    }

    /**
     * @return  bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Perspective_TabbedWidget::index_delete');
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $id = $this->getRequest()->getParam('condition_id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->conditionModel;
                $this->conditionResourceModel->load($model, $id);
                $this->conditionResourceModel->delete($model);

                $this->messageManager->addSuccessMessage(__('Record deleted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('Record does not exist.'));
        return $resultRedirect->setPath('*/*/');
    }
}
