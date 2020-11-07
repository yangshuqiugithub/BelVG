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
 * Class BlockType
 * @package BelVG\ProductTabs\Model
 */
class BlockType extends \Magento\Framework\Model\AbstractModel
{
    const DEFAULT_DATA
        = [
            [
                'block_type_name' => 'Html Content',
                'block_type_class' => 'BelVG\ProductTabs\Block\Tab\Html',
                'block_type_template' => '',
                'block_name_for_deleting' => ''
            ],
            [
                'block_type_name' => 'Additional Information',
                'block_type_class' => 'Magento\Catalog\Block\Product\View\Attributes',
                'block_type_template' => 'Magento_Catalog::product/view/attributes.phtml',
                'block_name_for_deleting' => ''
            ],
            [
                'block_type_name' => 'Product Description',
                'block_type_class' => 'Magento\Catalog\Block\Product\View\Description',
                'block_type_template' => 'Magento_Catalog::product/view/description.phtml',
                'block_name_for_deleting' => ''
            ],
            [
                'block_type_name' => 'Product Reviews',
                'block_type_class' => 'BelVG\ProductTabs\Block\Tab\Product\Review',
                'block_type_template' => 'Magento_Review::review.phtml',
                'block_name_for_deleting' => ''
            ],
            [
                'block_type_name' => 'Related Products',
                'block_type_class' => 'Magento\Catalog\Block\Product\ProductList\Related',
                'block_type_template' => 'Magento_Catalog::product/list/items.phtml',
                'block_name_for_deleting' => 'catalog.product.related'
            ],
            [
                'block_type_name' => 'We Also Recommended',
                'block_type_class' => 'Magento\Catalog\Block\Product\ProductList\Upsell',
                'block_type_template' => 'Magento_Catalog::product/list/items.phtml',
                'block_name_for_deleting' => 'product.info.upsell'
            ]
        ];

    protected function _construct()
    {
        $this->_init(\BelVG\ProductTabs\Model\ResourceModel\BlockType::class);
    }
}
