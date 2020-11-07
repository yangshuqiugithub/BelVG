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
 * Class Save
 *
 * @package BelVG\ProductTabs\Controller\Adminhtml\Tab
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'BelVG_ProductTabs::tab_save';

    /**
     *
     */
    const ONESELF_REDIRECT = 0;
    /**
     *
     */
    const BACK_REDIRECT = 1;
    /**
     *
     */
    const NEW_REDIRECT = 2;

    /**
     * @var \BelVG\ProductTabs\Model\TabFactory
     */
    protected $tabModelFactory;

    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\TabFactory
     */
    protected $tabResourceModelFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    protected $customerSession;

    protected $productTabsDataHelper;

    public function __construct(
        \BelVG\ProductTabs\Helper\Data $productTabsDataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \BelVG\ProductTabs\Model\TabFactory $tabModelFactory,
        \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->productTabsDataHelper = $productTabsDataHelper;
        $this->customerSession = $customerSession;
        $this->jsonSerializer = $jsonSerializer;
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


        /** @var \BelVG\ProductTabs\Model\Tab $tabModel */
        $tabModel = $this->tabModelFactory->create();


        /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab $tabResourceModel */
        $tabResourceModel = $this->tabResourceModelFactory->create();

        /** @var array $tabData */
        $tabData = $this->getTabData();

        $tabId = (int)$this->getRequest()->getParam('tab_id');

        $redirectId = (int)$this->getRequest()->getParam('back');

        if (0 !== $tabId) {
            try {
                $tabResourceModel->load($tabModel, $tabId);
                $tabModel->addData($tabData);
                $tabResourceModel->save($tabModel);
                $this->messageManager->addSuccessMessage(__('Tab has been saved'));
                $this->customerSession->unsTabGridData();

                return $this->getSuccessResult($redirectId, $tabId);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['tab_id' => $tabId]);
            }
        } else {
            try {
                $tabModel->setData($tabData);
                $tabResourceModel->save($tabModel);
                $tabId = (int)$tabModel->getId();
                $this->messageManager->addSuccessMessage(__('Tab has been added'));
                $this->customerSession->unsTabGridData();

                return $this->getSuccessResult($redirectId, $tabId);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['tab_id' => $tabId]);
            }
        }
    }

    /**
     * @return array
     */
    protected function getTabData(): array
    {
        $requestParams = $this->getRequest()->getParams();

        $data = [
            'title' => $requestParams['title'],
            'block_type' => $requestParams['block_type'],
            'sort_order' => $requestParams['sort_order'],
            'selection_of_products' => (int)$requestParams['selection_of_products'],
            'is_active' => $requestParams['is_active'],
            'store_id' => $requestParams['store_id'],
            'customer_groups' => $requestParams['customer_groups']
        ];

        $data['alias'] = $this->productTabsDataHelper->getTabAlias($data['alias'] ?? false, $data['title']);

        //Add excluded product's ids or the individual product's id
        if ($data['selection_of_products'] === 1) {
            if (isset($requestParams['links'])) {
                $data['excluded_products'] = $requestParams['links'];
            } else {
                $data['excluded_products'] = '';
            }
            $data['product_id'] = '';
        } elseif ($data['selection_of_products'] === 2) {
            $data['product_id'] = $requestParams['product_id'];
            $data['excluded_products'] = '';
        }

        //Add the content if block_type is Html
        switch ($requestParams['block_type']) {
            case 1:
                $data['content'] = $requestParams['content'];
                break;
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                $data['content'] = '';
        }

        return $data;
    }

    /**
     * @param $redirectId
     * @param $currentId
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function getSuccessResult($redirectId, $currentId)
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        switch ($redirectId){
            case self::ONESELF_REDIRECT:
                return $resultRedirect->setPath('*/*/edit', ['tab_id' => $currentId]);
            case self::BACK_REDIRECT:
                return $resultRedirect->setPath('*/index/grid');
            case self::NEW_REDIRECT:
                return $resultRedirect->setPath('*/*/edit', ['tab_id' => null]);
        }
    }
}
