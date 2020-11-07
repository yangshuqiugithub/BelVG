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

namespace BelVG\ProductTabs\Block\Form\Element;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class Chooser
 * @package BelVG\ProductTabs\Block\Form\Element
 */
class Chooser extends Generic implements TabInterface
{


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare content for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Product');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Product');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    protected function getFieldsetId()
    {
        return 'fieldset_product';
    }

    /**
     * @return Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');


        $uniqId = $this->mathRandom->getUniqueHash($this->getId());
        $sourceUrl = $this->getUrl(
            'catalog/product_widget/chooser',
            ['uniq_id' => $uniqId, 'use_massaction' => false]
        );


        $fieldset = $form->addFieldset(
            $this->getFieldsetId(),
            ['legend' => __('Choose product'),'class'=>'no-display']
        );

        $chooser = $this->getLayout()->createBlock(
            \BelVG\ProductTabs\Block\Form\Element\Widget\Chooser::class
        )->setElement(
            $fieldset
        )->setConfig(
            [
                'button' => [
                    'open' => __('Select Products...'),
                    'type' => '\Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser',
                ]
            ]
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        $this->setChild('form_after', $chooser);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

