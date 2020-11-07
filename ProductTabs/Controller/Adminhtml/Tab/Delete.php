<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   BelVG
 * @package    BelVG_ProductTabs
 * @copyright  Copyright (c) BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
namespace BelVG\ProductTabs\Controller\Adminhtml\Tab;

/**
 * Class Delete
 *
 * @package BelVG\ProductTabs\Controller\Adminhtml\Tab
 */
class Delete extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'BelVG_ProductTabs::tab_delete';

    /**
     * @var \BelVG\ProductTabs\Model\TabFactory
     */
    protected $tabModelFactory;

    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\TabFactory
     */
    protected $tabResourceModelFactory;

    /**
     * Delete constructor.
     * @param \BelVG\ProductTabs\Model\TabFactory $tabModelFactory
     * @param \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \BelVG\ProductTabs\Model\TabFactory $tabModelFactory,
        \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->tabModelFactory = $tabModelFactory;
        $this->tabResourceModelFactory = $tabResourceModelFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('tab_id');
        if ($id) {
            try {
                /** @var \BelVG\ProductTabs\Model\Tab $tabModel */
                $tabModel = $this->tabModelFactory->create();

                /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab $tabResourceModel */
                $tabResourceModel = $this->tabResourceModelFactory->create();

                $tabResourceModel->load($tabModel, $id);
                $tabResourceModel->delete($tabModel);

                // display success message
                $this->messageManager->addSuccessMessage(__('Tab has been deleted.'));
                // go to grid
                return $resultRedirect->setPath('*/index/grid');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['tab_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a tab to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/index/grid');
    }
}
