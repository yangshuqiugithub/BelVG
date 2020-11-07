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
 * Class InstallData
 *
 * @package BelVG\ProductTabs\Setup
 */
class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var \BelVG\ProductTabs\Model\BlockTypeFactory
     */
    protected $blockTypeModelFactory;

    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\BlockTypeFactory
     */
    protected $blockTypeResourceModelFactory;

    /**
     * @var \BelVG\ProductTabs\Model\TabFactory
     */
    protected $tabModelFactory;

    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\TabFactory
     */
    protected $tabResourceModelFactory;

    /**
     * @var \BelVG\ProductTabs\Model\Tab\Source\BlockType
     */
    protected $blockTypeData;

    /**
     * InstallData constructor.
     * @param \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory
     * @param \BelVG\ProductTabs\Model\TabFactory $tabModelFactory
     * @param \BelVG\ProductTabs\Model\Tab\Source\BlockType $blockTypeData
     * @param \BelVG\ProductTabs\Model\ResourceModel\BlockTypeFactory $blockTypeResourceModelFactory
     * @param \BelVG\ProductTabs\Model\BlockTypeFactory $blockTypeModelFactory
     */
    public function __construct(
        \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory,
        \BelVG\ProductTabs\Model\TabFactory $tabModelFactory,
        \BelVG\ProductTabs\Model\Tab\Source\BlockType $blockTypeData,
        \BelVG\ProductTabs\Model\ResourceModel\BlockTypeFactory $blockTypeResourceModelFactory,
        \BelVG\ProductTabs\Model\BlockTypeFactory $blockTypeModelFactory
    )
    {
        $this->tabResourceModelFactory = $tabResourceModelFactory;
        $this->tabModelFactory = $tabModelFactory;
        $this->blockTypeData = $blockTypeData;
        $this->blockTypeModelFactory = $blockTypeModelFactory;
        $this->blockTypeResourceModelFactory = $blockTypeResourceModelFactory;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function install(\Magento\Framework\Setup\ModuleDataSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        /** @var \BelVG\ProductTabs\Model\ResourceModel\BlockType $blockTypeResourceModel */
        $blockTypeResourceModel = $this->blockTypeResourceModelFactory->create();

        foreach (\BelVG\ProductTabs\Model\BlockType::DEFAULT_DATA as $blockTypeData){
            /** @var \BelVG\ProductTabs\Model\BlockType $blockTypeModel */
            $blockTypeModel = $this->blockTypeModelFactory->create();
            $blockTypeModel->setData($blockTypeData);
            $blockTypeResourceModel->save($blockTypeModel);
        }

        /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab $tabResourceModel */
        $tabResourceModel = $this->tabResourceModelFactory->create();

        foreach (\BelVG\ProductTabs\Model\Tab::DEFAULT_DATA as $tabData){
            /** @var \BelVG\ProductTabs\Model\Tab $tabModel */
            $tabModel = $this->tabModelFactory->create();
            $tabModel->setData($tabData);
            $tabResourceModel->save($tabModel);
            $setup->getConnection()->insert(
                $setup->getConnection()->getTableName('belvg_producttabs_store'),
                ['tab_id' => $tabModel->getId(), 'store_id' => 0]
            );

            $setup->getConnection()->insert(
                $setup->getConnection()->getTableName('belvg_producttabs_customer_groups'),
                ['tab_id' => $tabModel->getId(), 'group_id' => 32000]
            );

        }
    }
}
