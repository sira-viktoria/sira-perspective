<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\TabbedWidget\Block\Adminhtml\Index\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Delete Button.
 */
class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        $condition_id = $this->context->getRequest()->getParam('condition_id');

        if ($condition_id) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        $condition_id = $this->context->getRequest()->getParam('condition_id');
        return $this->getUrl('*/*/delete', ['condition_id' => $condition_id]);
    }
}
