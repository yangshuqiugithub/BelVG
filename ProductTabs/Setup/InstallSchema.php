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
namespace BelVG\ProductTabs\Setup;

/**
 * Class InstallSchema
 * @package BelVG\ProductTabs\Setup
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        //START table setup
        /**
         * Create table belvg_producttabs_tab
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('belvg_producttabs_tab')
        )->addColumn(
            'tab_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Tab ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Tab Title'
        )->addColumn(
            'alias',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Tab Alias'
        )->addColumn(
            'block_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false,],
            'Block Type'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Sort Order'
        )->addColumn(
            'selection_of_products',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            '1 - all products, 2 - individual'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Product Id for individual selection_of_products'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Content'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0',],
            'Is Active'
        )->setComment('BelVG Product Tabs Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table belvg_producttabs_block_type
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('belvg_producttabs_block_type')
        )->addColumn(
            'block_type_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Tab ID'
        )->addColumn(
            'block_type_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Block Type name'
        )->addColumn(
            'block_type_class',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Block Type class'
        )->addColumn(
            'block_type_template',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Block Type template'
        )->addColumn(
            'block_name_for_deleting',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Block name for deleting'
        )->setComment('Block Type table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table belvg_producttabs_store
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('belvg_producttabs_store')
        )->addColumn(
            'tab_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Tab ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('belvg_producttabs_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('belvg_producttabs_store', 'tab_id', 'belvg_producttabs_tab', 'tab_id'),
            'tab_id',
            $installer->getTable('belvg_producttabs_tab'),
            'tab_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('belvg_producttabs_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Store Ids table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table belvg_producttabs_customer_groups
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('belvg_producttabs_customer_groups')
        )->addColumn(
            'tab_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Tab ID'
        )->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Group Id'
        )->addIndex(
            $installer->getIdxName('belvg_producttabs_customer_groups', ['group_id']),
            ['group_id']
        )->addForeignKey(
            $installer->getFkName('belvg_producttabs_customer_groups', 'tab_id', 'belvg_producttabs_tab', 'tab_id'),
            'tab_id',
            $installer->getTable('belvg_producttabs_tab'),
            'tab_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Group Ids table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table belvg_producttabs_excluded_products
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('belvg_producttabs_excluded_products')
        )->addColumn(
            'tab_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Tab ID'
        )->addColumn(
            'excluded_product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Excluded Product Id'
        )->addIndex(
            $installer->getIdxName('belvg_producttabs_excluded_products', ['excluded_product_id']),
            ['excluded_product_id']
        )->addForeignKey(
            $installer->getFkName('belvg_producttabs_excluded_products', 'tab_id', 'belvg_producttabs_tab', 'tab_id'),
            'tab_id',
            $installer->getTable('belvg_producttabs_tab'),
            'tab_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('belvg_producttabs_excluded_products', 'excluded_product_id', 'catalog_product_entity', 'entity_id'),
            'excluded_product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Excluded Product Ids table'
        );
        $installer->getConnection()->createTable($table);

        //END   table setup
        $installer->endSetup();
    }
}
