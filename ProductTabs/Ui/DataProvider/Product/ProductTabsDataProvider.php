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
namespace BelVG\ProductTabs\Ui\DataProvider\Product;

/**
 * Class ProductTabsDataProvider
 *
 * @package BelVG\ProductTabs\Ui\DataProvider\Product
 */
class ProductTabsDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     * @since 100.1.0
     */
    protected $request;

    protected $customerSession;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Customer\Model\Session $customerSession,
        \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
        $this->customerSession = $customerSession;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if ($this->customerSession->getTabGridData() === null) {
            $this->getCollection()->addEntityFilter($this->request->getParam('current_product_id', 0));

            $arrItems = [
                'totalRecords' => $this->getCollection()->getSize(),
                'items' => [],
            ];

            foreach ($this->getCollection() as $item) {
                $arrItems['items'][] = $item->toArray([]);
            }

            $this->customerSession->setTabGridData($arrItems);
        }

        return $this->customerSession->getTabGridData();
    }
}
