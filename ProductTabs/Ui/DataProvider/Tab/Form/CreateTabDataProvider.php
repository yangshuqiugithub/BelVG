<?php

namespace BelVG\ProductTabs\Ui\DataProvider\Tab\Form;

class CreateTabDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $urlBuilder;

    protected $request;

    protected $customerSession;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\UrlInterface $urlBuilder,
        \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab\Collection collection */
        $this->collection = $collectionFactory->create();
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->customerSession = $customerSession;
    }

    public function getData(): array
    {
        $currentTabData = [];
        $rowId = $this->request->getParam('row_id');
        if ($rowId !== null && $rowId == -1) {
            $currentTabData = [
                'is_active' => '',
                'title' => 'Enter you title ',
                'alias' => '',
                'block_type' => 1,
                'sort_order' => '100',
                'selection_of_products' => 2,
                'store_id' => '',
                'customer_groups' => '',
                'content' => 'Some text'
            ];

        }

        if ($rowId !== null && $rowId >= 0) {
            $tabsData = $this->customerSession->getTabGridData();
            $currentTabData = $tabsData['items'][$rowId];
            $currentTabData['row_id'] = $rowId;
        }

        $this->data = array_replace_recursive(
            $this->data,
            [
                'config' => [
                    'data' => $currentTabData
                ]
            ]
        );

        return $this->data;
    }

    public function getMeta()
    {
        return parent::getMeta();
    }
}
