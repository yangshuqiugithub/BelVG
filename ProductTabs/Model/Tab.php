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
namespace BelVG\ProductTabs\Model;

/**
 * Class Tab
 * @package BelVG\ProductTabs\Model
 */
class Tab extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const DEFAULT_DATA
        = [
            [
                'title' => 'Details',
                'alias' => 'product_info_description',
                'block_type' => 3,
                'sort_order' => 10,
                'selection_of_products' => 1,
                'content' => null,
                'is_active' => 1,
                'product_id' => 0
            ],
            [
                'title' => 'More Information',
                'alias' => 'additional_info',
                'block_type' => 2,
                'sort_order' => 20,
                'selection_of_products' => 1,
                'content' => null,
                'is_active' => 1,
                'product_id' => 0
            ],
            [
                'title' => 'Reviews',
                'alias' => 'reviews',
                'block_type' => 4,
                'sort_order' => 30,
                'selection_of_products' => 1,
                'content' => null,
                'is_active' => 1,
                'product_id' => 0
            ],
        ];

    const CACHE_TAG = 'belvg_product_tab';

    protected function _construct()
    {
        $this->_init(\BelVG\ProductTabs\Model\ResourceModel\Tab::class);
    }

    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
