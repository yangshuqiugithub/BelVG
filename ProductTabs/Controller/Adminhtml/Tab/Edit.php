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
 * Class Edit
 *
 * @package BelVG\ProductTabs\Controller\Adminhtml\Tab
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'BelVG_ProductTabs::tab_view';

    /**
     * @var \BelVG\ProductTabs\Model\TabFactory
     */
    protected $tabModelFactory;

    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\TabFactory
     */
    protected $tabResourceModelFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     * @param \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory
     * @param \BelVG\ProductTabs\Model\TabFactory $tabModelFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory,
        \BelVG\ProductTabs\Model\TabFactory $tabModelFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->tabResourceModelFactory = $tabResourceModelFactory;
        $this->tabModelFactory = $tabModelFactory;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('tab_id');

        /** @var \BelVG\ProductTabs\Model\Tab $tabModel */
        $tabModel = $this->tabModelFactory->create();

        /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab $tabResourceModel */
        $tabResourceModel = $this->tabResourceModelFactory->create();

        // 2. Initial checking
        if ($id) {
            $tabResourceModel->load($tabModel, $id);
            if (!$tabModel->getId()) {
                $this->messageManager->addErrorMessage(__('This tab no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/index/grid');
            }
        }

        $this->coreRegistry->register('product_tab', $tabModel);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $this->initTab($resultPage)->addBreadcrumb(
            $id ? __('Edit Tab') : __('New Tab'),
            $id ? __('Edit Tab') : __('New Tab')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Tabs'));
        $resultPage->getConfig()->getTitle()->prepend(
            $tabModel->getId() ? $tabModel->getData('title') : __('New Tab')
        );

        return $resultPage;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initTab($resultPage): \Magento\Backend\Model\View\Result\Page
    {
        $resultPage->setActiveMenu('BelVG_ProductTabs::tab_management')
            ->addBreadcrumb(__('BelVG'), __('BelVG'))
            ->addBreadcrumb(__('Products'), __('Tab Management'))
        ;

        return $resultPage;
    }
}
