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
namespace BelVG\ProductTabs\Model\ResourceModel\BlockType;

/**
 * Class Collection
 * @package BelVG\ProductTabs\Model\ResourceModel\BlockType
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'block_type_id';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            \BelVG\ProductTabs\Model\BlockType::class,
            \BelVG\ProductTabs\Model\ResourceModel\BlockType::class
        );
    }
}
