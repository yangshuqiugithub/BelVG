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

namespace BelVG\ProductTabs\Helper;

/**
 * Class Config
 *
 * @package BelVG\ProductTabs\Helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        return 'product_tabs/settings/';
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->getConfig('enabled') ? true : false;
    }

    /**
     * @return bool
     */
    public function isAllOpen(): bool
    {
        return $this->getConfig('is_all_open') ? true : false;
    }

    /**
     * @return string
     */
    public function getDisplayType(): string
    {
        return $this->getConfig('display_type');
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    public function getConfig($field)
    {
        return $this->scopeConfig->getValue(
            $this->getConfigPath() . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
