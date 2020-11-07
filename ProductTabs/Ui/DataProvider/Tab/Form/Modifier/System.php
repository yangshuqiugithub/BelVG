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
namespace BelVG\ProductTabs\Ui\DataProvider\Tab\Form\Modifier;

/**
 * Class System
 * @package BelVG\ProductTabs\Ui\DataProvider\Tab\Form\Modifier
 */
class System implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    /**
     *
     */
    const KEY_SUBMIT_URL = 'submit_url';

    /** @var \Magento\Framework\UrlInterface  */
    protected $urlBuilder;

    /** @var array  */
    protected $productUrls = [
        self::KEY_SUBMIT_URL => 'belvg_product_tabs/tab/save',
    ];

    /**
     * System constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $productUrls
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        array $productUrls = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->productUrls = array_replace_recursive($this->productUrls, $productUrls);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        $submitUrl = $this->urlBuilder->getUrl($this->productUrls[self::KEY_SUBMIT_URL]);

        return array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                ]
            ]
        );
    }

    /**
     * @param array $meta
     *
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
