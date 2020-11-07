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
namespace BelVG\ProductTabs\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Ui\Component\Form;
use Magento\Framework\UrlInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\App\ObjectManager;

/**
 * Class ProductTabs
 * @package BelVG\ProductTabs\Ui\DataProvider\Product\Form\Modifier
 */
class ProductTabs extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     *
     */
    const GROUP_REVIEW = 'product_tabs';
    /**
     *
     */
    const GROUP_CONTENT = 'content';
    /**
     *
     */
    const DATA_SCOPE_REVIEW = 'grouped';
    /**
     *
     */
    const SORT_ORDER = 20;
    /**
     *
     */
    const LINK_TYPE = 'associated';

    /**
     * @var LocatorInterface
     * @since 100.1.0
     */
    protected $locator;

    /**
     * @var UrlInterface
     * @since 100.1.0
     */
    protected $urlBuilder;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->locator->getProduct()->getId() || !$this->getModuleManager()->isOutputEnabled('Magento_Review')) {
            return $meta;
        }

        $meta[static::GROUP_REVIEW] = [
            'children' => [
                'product_tab_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'product_tabs_listing',
                                'externalProvider' => 'product_tabs_listing.product_tabs_listing_data_source',
                                'selectionsProvider' => 'product_tabs_listing.product_tabs_listing.product_columns.ids',
                                'ns' => 'product_tabs_listing',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id'
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Product Tabs'),
                        'collapsible' => true,
                        'opened' => false,
                        'componentType' => Form\Fieldset::NAME,
                        'sortOrder' =>
                            $this->getNextGroupSortOrder(
                                $meta,
                                static::GROUP_CONTENT,
                                static::SORT_ORDER
                            ),
                    ],
                ],
            ],
        ];

        return $meta;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;

        return $data;
    }

    /**
     * Retrieve module manager instance using dependency lookup to keep this class backward compatible.
     *
     * @return ModuleManager
     *
     * @deprecated 100.2.0
     */
    private function getModuleManager()
    {
        if ($this->moduleManager === null) {
            $this->moduleManager = ObjectManager::getInstance()->get(ModuleManager::class);
        }
        return $this->moduleManager;
    }
}