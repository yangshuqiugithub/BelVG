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
namespace BelVG\ProductTabs\Ui\DataProvider\Tab\Form;

/**
 * Class TabDataProvider
 * @package BelVG\ProductTabs\Ui\DataProvider\Tab\Form
 */
class TabDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var
     */
    protected $loadedData;

    /**
     * @var \Magento\Ui\DataProvider\Modifier\PoolInterface
     */
    private $pool;

    /**
     * TabDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\Modifier\PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $collectionFactory,
        \Magento\Ui\DataProvider\Modifier\PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab\Collection collection */
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if(isset($this->loadedData)){
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $tab){
            $this->loadedData[$tab->getId()] = $tab->getData();
        }

        $data = parent::getData();
        $dataSource = reset($data['items']);
        $id = $dataSource[$this->getRequestFieldName()];

        $this->data = array(
            $id => $dataSource
        );


        /** @var \Magento\Ui\DataProvider\Modifier\ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var \Magento\Ui\DataProvider\Modifier\ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
