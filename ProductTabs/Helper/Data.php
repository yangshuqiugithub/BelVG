<?php

namespace BelVG\ProductTabs\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getTabAlias($alias, $title)
    {
        if ($alias) {
            $preparedAlias = preg_replace('/[^A-Za-z0-9_]+/', '_', $alias);
        } else {
            $preparedAlias = preg_replace('/[^A-Za-z0-9_]+/', '_', $title);
        }

        return $preparedAlias;
    }
}