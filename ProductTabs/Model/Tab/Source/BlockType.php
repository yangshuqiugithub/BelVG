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
namespace BelVG\ProductTabs\Model\Tab\Source;

/**
 * Class BlockType
 * @package BelVG\ProductTabs\Model\Tab\Source
 */
class BlockType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Html Content')],
            ['value' => 2, 'label' => __('Additional Information')],
            ['value' => 3, 'label' => __('Product Description')],
            ['value' => 4, 'label' => __('Product Reviews')],
            ['value' => 5, 'label' => __('Related Products')],
            ['value' => 6, 'label' => __('We Also Recommended')]
        ];
    }
}
