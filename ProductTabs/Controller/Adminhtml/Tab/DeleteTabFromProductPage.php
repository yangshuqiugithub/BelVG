<?php

namespace BelVG\ProductTabs\Controller\Adminhtml\Tab;

class DeleteTabFromProductPage extends \Magento\Framework\App\Action\Action
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
        $rowId = (int)$this->getRequest()->getParam('rowId');
        $tabGridData = $this->customerSession->getTabGridData();
        $items = [];
        foreach ($tabGridData['items'] as $key => $itemData){
            if($key === $rowId){
                continue;
            }
            $items[] = $itemData;
        }

        $tabGridData['items'] = $items;
        $this->customerSession->setTabGridData($tabGridData);

        return $resultJson->setData(
            [
                'messages' => '',
                'error' => '',
            ]
        );
    }
}
