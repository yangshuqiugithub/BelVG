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

namespace BelVG\ProductTabs\Block\Tab\Product;

/**
 * Class Review
 *
 * @package BelVG\ProductTabs\Block\Tab\Product
 */
class Review extends \Magento\Review\Block\Product\Review
{
    /**
     * @var array
     */
    protected $reviewFormBlockMap
        = [
            'default' => 'Magento\Review\Block\Form',
            'checkout_cart_configure' => 'Magento\Review\Block\Form\Configure',
            'wishlist_index_configure' => 'Magento\Review\Block\Form\Configure'
        ];

    /**
     * @return \Magento\Review\Block\Product\Review
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $currentAction = $this->getRequest()->getFullActionName();
        $formBlock = array_key_exists($currentAction, $this->reviewFormBlockMap)
            ?
            $this->reviewFormBlockMap[$currentAction]
            :
            $this->reviewFormBlockMap['default'];

        $layout = $this->getLayout();
        $blockName = 'belvg.product.' . $this->getNameInLayout();
        $reviewForm = $layout->createBlock(
            $formBlock,
            $blockName,
            [
                'data' =>
                    [
                        'jsLayout' =>
                            [
                                'components' =>
                                    [
                                        'review-form' =>
                                            ['component' => 'Magento_Review/js/view/review']
                                    ]
                            ]
                    ]
            ]
        );
        if ($reviewForm) {
            $this->setChild('review_form', $reviewForm);
            $containerName = $blockName . '.fields.before';
            $layout->addContainer($containerName, 'Review Form Fields Before');
            $layout->setChild($blockName, $containerName, 'form_fields_before');
        }

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        $this->setTabTitle();

        return $this->getTitle();
    }
}
