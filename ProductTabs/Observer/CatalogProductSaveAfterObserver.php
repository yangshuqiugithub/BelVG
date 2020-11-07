<?php

namespace BelVG\ProductTabs\Observer;

/**
 * Class CatalogProductSaveAfterObserver
 *
 * @package BelVG\ProductTabs\Observer
 */
class CatalogProductSaveAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $tabModelFactory;

    protected $customerSession;

    protected $tabCollectionFactory;

    protected $productTabsDataHelper;

    public function __construct(
        \BelVG\ProductTabs\Helper\Data $productTabsDataHelper,
        \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $tabCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \BelVG\ProductTabs\Model\TabFactory $tabModelFactory
    ) {
        $this->productTabsDataHelper = $productTabsDataHelper;
        $this->tabCollectionFactory = $tabCollectionFactory;
        $this->customerSession = $customerSession;
        $this->tabModelFactory = $tabModelFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        if ($tabGridData = $this->customerSession->getTabGridData()) {
            /** @var array $allTabs */
            $allTabs = $tabGridData['items'];
            $newTabs = [];
            $changedTabs = [];
            $allIndividualsTabsIds = [];
            $allIndividualsTabsIdsFromDb = [];

            $product = $observer->getProduct();
            $productId = (int)$product->getId();

            /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab\Collection $tabCollection */
            $tabCollection = $this->tabCollectionFactory->create();
            $tabCollection->addFieldToFilter('selection_of_products', 2);
            $tabCollection->addFieldToFilter('product_id', $productId);
            $collectionArray = $tabCollection->toArray();
            foreach ($collectionArray['items'] as $itemData){
                $allIndividualsTabsIdsFromDb[] = $itemData['tab_id'];
            }

            foreach ($allTabs as $key => $tabData) {
                if((int)$tabData['selection_of_products'] === 2){
                    if ((int)$tabData['tab_id'] === 0) {
                        $newTabs[] = $allTabs[$key];
                    }

                    if ((int)$tabData['tab_id'] !== 0 && (int)$tabData['product_id'] === 0) {
                        $changedTabs[] = $allTabs[$key];
                    }

                    if((int)$tabData['product_id'] === $productId){
                        $allIndividualsTabsIds[] = $allTabs[$key]['tab_id'];
                    }
                }
            }

            $deleteTabIds = array_diff($allIndividualsTabsIdsFromDb, $allIndividualsTabsIds);

            //Delete tabs
            if (count($deleteTabIds) !== 0) {
                foreach ($deleteTabIds as $tabId) {
                    /** @var \BelVG\ProductTabs\Model\Tab $tabModel */
                    $tabModel = $this->tabModelFactory->create();
                    $tabModel->load($tabId);
                    $tabModel->delete();
                }
            }

            //Change old tabs
            if (count($changedTabs) !== 0) {
                $product = $observer->getProduct();
                $productId = $product->getId();

                foreach ($changedTabs as $changedTabData) {
                    /** @var \BelVG\ProductTabs\Model\Tab $tabModel */
                    $tabModel = $this->tabModelFactory->create();
                    $tabModel->load($changedTabData['tab_id']);
                    $preparedData = $this->prepareData($changedTabData, $productId);
                    $tabModel->addData($preparedData);
                    $tabModel->save();
                }
            }

            //Add new tabs
            if (count($newTabs) !== 0) {
                $product = $observer->getProduct();
                $productId = $product->getId();

                foreach ($newTabs as $newTabData) {
                    /** @var \BelVG\ProductTabs\Model\Tab $tabModel */
                    $tabModel = $this->tabModelFactory->create();
                    $preparedData = $this->prepareData($newTabData, $productId);
                    $tabModel->setData($preparedData);
                    $tabModel->save();
                }
            }

            $this->customerSession->unsTabGridData();
        }
    }

    protected function prepareData(array $tabData, int $productId): array
    {
        $data = [
            'title' => $tabData['title'],
            'block_type' => $tabData['block_type'],
            'sort_order' => $tabData['sort_order'],
            'selection_of_products' => 2,
            'is_active' => $tabData['is_active'],
            'store_id' => $tabData['store_id'],
            'customer_groups' => $tabData['customer_groups'],
            'product_id' => $productId
        ];

        $data['alias'] = $this->productTabsDataHelper->getTabAlias($data['alias'] ?? false, $data['title']);

        //Add the content if block_type is Html
        switch ($tabData['block_type']) {
            case 1:
                $data['content'] = $tabData['content'];
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
}