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
namespace BelVG\ProductTabs\Model\ResourceModel;

/**
 * Class Tab
 * @package BelVG\ProductTabs\Model\ResourceModel
 */
class Tab extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('belvg_producttabs_tab', 'tab_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        //Save Store Ids
        if($object->getData('store_id') ?? false){
            $this->saveBindingData(
                $object,
                'store_id',
                'belvg_producttabs_store',
                'store_id'
            );
        }

        //Save Customer Group Ids
        if($object->getData('customer_groups') ?? false) {
            $this->saveBindingData(
                $object,
                'customer_groups',
                'belvg_producttabs_customer_groups',
                'group_id'
            );
        }

        //Save Excluded Product Ids
        if($object->getData('excluded_products') ?? false) {
            $this->saveBindingData(
                $object,
                'excluded_products',
                'belvg_producttabs_excluded_products',
                'excluded_product_id'
            );
        }

        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param string $dataKey
     * @param string $tableName
     * @param string $columnName
     */
    protected function saveBindingData(
        \Magento\Framework\Model\AbstractModel $object,
        string $dataKey,
        string $tableName,
        string $columnName
    ): void {
        $oldIds = $this->getBindingData(
            $object->getData('tab_id'),
            $tableName,
            $columnName
        );

        if ($dataKey === 'excluded_products') {
            $excludedProducts = $object->getData($dataKey);
            foreach ($excludedProducts['related'] as $data) {
                $newIds[] = $data['id'];
            }
        } else {
            $newIds = (array)$object->getData($dataKey);
        }

        $table = $this->getTable($this->getConnection()->getTableName($tableName));
        $insert = array_diff($newIds, $oldIds);
        $delete = array_diff($oldIds, $newIds);

        if ($delete) {
            $where = ['tab_id = ?' => (int)$object->getId(), $columnName . ' IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $id) {
                $data[] = ['tab_id' => (int)$object->getId(), $columnName => (int)$id];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * @param $tabId
     * @param $tableName
     * @param $colName
     * @return array
     */
    public function getBindingData($tabId, $tableName, $colName): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable($this->getConnection()->getTableName($tableName)),
            $colName
        )->where(
            'tab_id = :tab_id'
        );
        $binds = [':tab_id' => (int)$tabId];

        return $connection->fetchCol($select, $binds);
    }
}
