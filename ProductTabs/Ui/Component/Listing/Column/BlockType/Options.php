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
namespace BelVG\ProductTabs\Ui\Component\Listing\Column\BlockType;

/**
 * Class Options
 *
 * @package BelVG\ProductTabs\Ui\Component\Listing\Column\Status
 */
class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var  array */
    protected $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [
                [
                    'value' => 1,
                    'label' => 'Html Content'
                ],
                [
                    'value' => 2,
                    'label' => 'Additional Information'
                ],
                [
                    'value' => 3,
                    'label' => 'Product Description'
                ],
                [
                    'value' => 4,
                    'label' => 'Product Product Attribute'
                ],
                [
                    'value' => 5,
                    'label' => 'Product Reviews'
                ],
                [
                    'value' => 6,
                    'label' => 'Related Products'
                ],
                [
                    'value' => 7,
                    'label' => 'We Also Recommended'
                ],
            ];
        }

        return $this->options;
    }
}
