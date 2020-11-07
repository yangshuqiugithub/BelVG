<?php

namespace BelVG\ProductTabs\Block\Adminhtml\Product\Edit\Button;

/**
 * Class CreateCategory
 *
 * @package Magento\Catalog\Block\Adminhtml\Product\Edit\Button
 */
class CreateTab extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Save Tab'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 10
        ];
    }
}
