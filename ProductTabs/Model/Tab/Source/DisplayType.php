<?php

namespace BelVG\ProductTabs\Model\Tab\Source;

/**
 * Class Layout
 *
 * @package BelVG\ProductTabs\Model\Tab\Source
 */
class DisplayType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray(): array
    {
        $options = [];

        $availableOptions = [
            'tabs' => __('Tabs'),
            'accordion' => __('Accordion')
        ];

        foreach ($availableOptions as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $options;
    }
}
