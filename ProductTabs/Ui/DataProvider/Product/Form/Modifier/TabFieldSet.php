<?php

namespace BelVG\ProductTabs\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Modal;

class TabFieldSet extends AbstractModifier
{

    // Components indexes
    const CUSTOM_FIELDSET_INDEX = 'product_tabs_fieldset';
    const CUSTOM_FIELDSET_CONTENT = 'custom_fieldset_content';
    const CONTAINER_HEADER_NAME = 'custom_fieldset_content_header';

    // Fields names
    const FIELD_NAME_TEXT = 'example_text_field';
    const FIELD_NAME_SELECT = 'example_select_field';
    const FIELD_NAME_MULTISELECT = 'example_multiselect_field';

    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var array
     */
    protected $meta = [];

    protected $storeManager;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager     $arrayManager
     * @param UrlInterface     $urlBuilder
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder
    ) {
        $this->storeManager = $storeManager;
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Data modifier, does nothing in our example.
     *
     * @param array $data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;

        return $data;
    }

    /**
     * Meta-data modifier: adds ours fieldset
     *
     * @param array $meta
     *
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->addCustomFieldset();

        return $this->meta;
    }

    /**
     * Merge existing meta-data with our meta-data (do not overwrite it!)
     *
     * @return void
     */
    protected function addCustomFieldset()
    {
        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::CUSTOM_FIELDSET_INDEX => $this->getFieldsetConfig(),
            ]
        );
    }

    /**
     * Declare ours fieldset config
     *
     * @return array
     */
    protected function getFieldsetConfig()
    {
        $content = __(
            'Product Tabs'
        );

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Product Tabs'),
                        'componentType' => Fieldset::NAME,
                        'dataScope' => static::DATA_SCOPE_PRODUCT, // save data in the product data
                        'provider' => static::DATA_SCOPE_PRODUCT . '_data_source',
                        'ns' => static::FORM_NAME,
                        'collapsible' => true,
                        'sortOrder' => 10,
                        'opened' => false,
                    ],
                ],
            ],
            'children' => [
                'header' => $this->getHeaderContainerConfig(10),
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Product Tab')
                ),
                'modal' => $this->getGenericModal(
                    __('Add Product Tab'),
                    'related'
                ),
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
                                'listens' => [
                                    'index=create_tab_item:responseData' => 'reload',
                                ],
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
        ];
    }

    protected function getButtonSet(Phrase $content, Phrase $buttonTitle): array
    {
        $modalTarget = 'product_form.product_form.product_tabs_fieldset.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_related' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.create_tab_item',
                                        'actionName' => 'updateData',
                                        'params'=>[
                                            ['row_id' => -1]
                                        ]
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.create_tab_item',
                                        'actionName' => 'resetForm'
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getGenericModal(Phrase $title, $scope)
    {
        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                        ],
                        'imports' => [
                            'state' => '!index=create_tab_item:responseStatus'
                        ],
                    ],
                ],
            ],
            'children' => [
                'create_tab_item' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => '',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/insert-form',
                                'dataScope' => '',
                                'update_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'render_url' => $this->urlBuilder->getUrl(
                                    'mui/index/render_handle',
                                    [
                                        'handle' => 'product_tab_create',
                                        'store' => $this->locator->getStore()->getId(),
                                        'buttons' => 1
                                    ]
                                ),
                                'autoRender' => false,
                                'ns' => 'new_tab_form',
                                'externalProvider' => 'new_tab_form.new_tab_form_data_source',
                                'toolbarContainer' => '${ $.parentName }',
                                'formSubmitType' => 'ajax',
                            ],
                        ],
                    ]
                ]
            ]
        ];

        return $modal;
    }

    protected function getHeaderContainerConfig($sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                        'content' => __('You can add Individual tab for current product. New tabs will be added after saving product.'),
                    ],
                ],
            ],
            'children' => [],
        ];
    }
}
