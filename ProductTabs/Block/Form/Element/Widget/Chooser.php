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

namespace BelVG\ProductTabs\Block\Form\Element\Widget;

/**
 * Class Chooser
 * @package BelVG\ProductTabs\Block\Form\Element\Widget
 */
class Chooser extends \Magento\Widget\Block\Adminhtml\Widget\Chooser
{
    const DATA_SOURCE = 'belvg_product_tabs_edit.belvg_product_tabs_edit_data_source';

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $element = $this->getElement();
        $chooserId = $this->getUniqId();
        $chooser = $element->getForm()->getElement('chooser' . $element->getId());
        $afterHtml = $chooser->getAfterElementHtml();
        return '<div class="admin__fieldset"><div class="admin__field">
            <label class="admin__field-label" ><span >Product</span></label>
            <div class="admin__field-control">
            '.$html .
            $afterHtml .
            '<script>
             //<![CDATA[
                require(["jquery","uiRegistry","prototype","mage/adminhtml/wysiwyg/widget"], function(jQuery, registry) {
                        jQuery(document).ready(function() {
                            var productLabel = registry.get("' . self::DATA_SOURCE . '").data.product_label;
                            jQuery("#' . $chooserId . 'label").text(productLabel);
                        });
                        
                        window.' . $chooserId . '.setElementValue = 
                            window.' . $chooserId . '.setElementValue.wrap(function(original_setElementValue, value) {
                                registry.get("' . self::DATA_SOURCE . '").data.product_id = value.replace("product/","");
                                return  original_setElementValue(value);
                            });
                });
            //]]>
            </script></div></div></div>';
    }
}
