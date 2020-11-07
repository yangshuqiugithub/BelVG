<?php

namespace BelVG\ProductTabs\Controller\Adminhtml\Tab;

class Add extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;

    protected $productTabsDataHelper;

    public function __construct(
        \BelVG\ProductTabs\Helper\Data $productTabsDataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->productTabsDataHelper = $productTabsDataHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        $requestParams = $this->getRequest()->getParams();
        $rowId = $requestParams['row_id'] ?? '';
        $tabId = $requestParams['tab_id'] ?? '';
        $tabData = [
            'title' => $requestParams['title'],
            'alias' => $requestParams['alias'],
            'block_type' => $requestParams['block_type'],
            'selection_of_products' => $requestParams['selection_of_products'],
            'sort_order' => $requestParams['sort_order'],
            'product_id' => 0,
            'content' => $requestParams['content'],
            'is_active' => $requestParams['is_active'],
            'store_id' => $requestParams['store_id'],
            'customer_groups' => $requestParams['customer_groups'],
        ];

        $tabData['alias'] = $this->productTabsDataHelper->getTabAlias($requestParams['alias'] ?? false, $requestParams['title']);

        if ($tabId !== '' && $tabId > 0) {
            $tabData['tab_id'] = $requestParams['tab_id'];
        } else {
            $tabData['tab_id'] = 0;
        }

        $tabGridData = $this->customerSession->getTabGridData();

        if ($rowId !== '' && $rowId >= 0) {
            $tabGridData['items'][$rowId] = $tabData;
        } else {
            $tabGridData['items'][] = $tabData;
            $tabGridData['totalRecords'] = ++$tabGridData['totalRecords'];
        }

        $this->customerSession->setTabGridData($tabGridData);

        return $resultJson->setData(
            [
                'messages' => '',
                'error' => '',
            ]
        );
    }
}