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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Request\Http as Request;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Class ExcludedProducts
 * @package BelVG\ProductTabs\Ui\DataProvider\Tab\Form\Modifier
 */
class ExcludedProducts extends AbstractModifier
{
    /**
     *
     */
    const DATA_SCOPE = '';
    /**
     *
     */
    const DATA_SCOPE_RELATED = 'related';
    /**
     *
     */
    const GROUP_RELATED = 'related';


    /**
     * @var UrlInterface
     * @since 101.0.0
     */
    protected $urlBuilder;


    /**
     * @var ImageHelper
     * @since 101.0.0
     */
    protected $imageHelper;

    /**
     * @var Status
     * @since 101.0.0
     */
    protected $status;

    /**
     * @var AttributeSetRepositoryInterface
     * @since 101.0.0
     */
    protected $attributeSetRepository;

    /**
     * @var string
     * @since 101.0.0
     */
    protected $scopeName;

    /**
     * @var string
     * @since 101.0.0
     */
    protected $scopePrefix;

    /** @var Request  */
    protected $_request;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Data
     */
    protected $pricingHelper;

    /**
     * ExcludedProducts constructor.
     * @param Data $pricingHelper
     * @param ResourceConnection $resourceConnection
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param Request $request
     * @param UrlInterface $urlBuilder
     * @param ImageHelper $imageHelper
     * @param Status $status
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        Request $request,
        UrlInterface $urlBuilder,
        ImageHelper $imageHelper,
        Status $status,
        AttributeSetRepositoryInterface $attributeSetRepository,
        $scopeName = '',
        $scopePrefix = ''
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->jsonSerializer = $jsonSerializer;
        $this->_request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->imageHelper = $imageHelper;
        $this->status = $status;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
    }

    /**
     * @param array $meta
     *
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'related' => [
                    'children' => [
                        'related_group' => $this->getRelatedFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Excluded Products'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => '',
                                'sortOrder' => 20
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function modifyData(array $data): array
    {
        $mainData = reset($data);
        if(!$mainData['tab_id'] ?? false){
            return $data;
        }

        $storeIds = $this->getStoreIds($mainData['tab_id']);
        if(count($storeIds) !== 0){
            $data[$mainData['tab_id']]['store_id'] = $storeIds;
        }

        $customerGroupsIds = $this->getCustomerGroupsIds($mainData['tab_id']);
        if(count($customerGroupsIds) !== 0){
            $data[$mainData['tab_id']]['customer_groups'] = $customerGroupsIds;
        }

        $excludedProductIds = $this->getExcludedProductIds($mainData['tab_id']);
        if(count($excludedProductIds) !== 0){
            $excludedProductData = $this->getExcludedProductData($excludedProductIds);

            $data[$mainData['tab_id']]['links']['related'] = $excludedProductData;
        }

        if ($mainData['product_id'] ?? false) {
            $product = $this->productRepository->getById((int)$mainData['product_id']);
            $productName = $product->getName();
            $data[$mainData['tab_id']]['product_label'] = $productName;
        }

        return $data;
    }

    /**
     * @param int $tabId
     * @return array
     */
    protected function getStoreIds(int $tabId): array
    {
        $storeIds = [];
        $tableName = $this->resourceConnection->getConnection()
            ->getTableName('belvg_producttabs_store');
        $data = $this->resourceConnection->getConnection()->fetchAssoc(
            'SELECT `store_id` FROM '.$tableName.' WHERE `tab_id` = ?',
            $tabId
        );
        foreach ($data as $storeId) {
            $storeIds[] = $storeId['store_id'];
        }

        return $storeIds;
    }

    /**
     * @param int $tabId
     * @return array
     */
    protected function getCustomerGroupsIds(int $tabId): array
    {
        $customerGroupIds = [];
        $tableName = $this->resourceConnection->getConnection()
            ->getTableName('belvg_producttabs_customer_groups');
        $data = $this->resourceConnection->getConnection()->fetchAssoc(
            'SELECT `group_id` FROM '.$tableName.' WHERE `tab_id` = ?',
            $tabId
        );
        foreach ($data as $customerGroup) {
            $customerGroupIds[] = $customerGroup['group_id'];
        }

        return $customerGroupIds;
    }

    /**
     * @param int $tabId
     * @return array
     */
    protected function getExcludedProductIds(int $tabId): array
    {
        $excludedProductIds = [];
        $tableName = $this->resourceConnection->getConnection()
            ->getTableName('belvg_producttabs_excluded_products');
        $data = $this->resourceConnection->getConnection()->fetchAssoc(
            'SELECT `excluded_product_id` FROM '.$tableName.' WHERE `tab_id` = ?',
            $tabId
        );
        foreach ($data as $excludedProduct) {
            $excludedProductIds[] = $excludedProduct['excluded_product_id'];
        }

        return $excludedProductIds;
    }

    /**
     * @param array $excludedProductIds
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getExcludedProductData(array $excludedProductIds): array
    {
        $excludedProductData = [];
        foreach ($excludedProductIds as $key => $excludedProductId) {
            $product = $this->productRepository->getById($excludedProductId);
            $imageHelper = $this->imageHelper->init($product, 'product_listing_thumbnail');
            $thumbnail = $imageHelper->getUrl();
            $excludedProductData[] = [
                'id' => $excludedProductId,
                'name' => $product->getName(),
                'status' => (int)$product->getStatus() === 1 ? 'Enabled' : 'Disabled',
                'attribute_set' => $this->attributeSetRepository->get($product->getAttributeSetId())->getAttributeSetName(),
                'sku' => $product->getSku(),
                'price' => $this->pricingHelper->currency($product->getPrice(), true, false),
                'thumbnail' => $thumbnail,
                'position' => ++$key,
                'record_id' => $excludedProductId,
                'initialize' => 'true'
            ];
        }

        return $excludedProductData;
    }

    /**
     * @return array
     */
    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_RELATED,
        ];
    }

    /**
     * @return array
     */
    protected function getRelatedFieldset()
    {
        $content = __(
            'Excluded Products'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Excluded Product')
                ),
                'modal' => $this->getGenericModal(
                    __('Add Excluded Product'),
                    'related'
                ),
                'related' => $this->getGrid(),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ]
        ];
    }

    /**
     * @param Phrase $content
     * @param Phrase $buttonTitle
     *
     * @return array
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle)
    {
        $modalTarget = 'belvg_product_tabs_edit.belvg_product_tabs_edit.related.related_group.modal';

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
                                        'targetName' => $modalTarget . '.related_product_listing',
                                        'actionName' => 'render',
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

    /**
     * @param Phrase $title
     * @param        $scope
     *
     * @return array
     */
    protected function getGenericModal(Phrase $title, $scope)
    {
        $listingTarget = $scope . '_product_listing';

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Products'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.product_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * @return array
     */
    protected function getGrid()
    {
        $dataProvider = 'related_product_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => true,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'entity_id',
                            'name' => 'name',
                            'status' => 'status_text',
                            'attribute_set' => 'attribute_set_text',
                            'sku' => 'sku',
                            'price' => 'price',
                            'thumbnail' => 'thumbnail_src',
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'thumbnail' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'elementTmpl' => 'ui/dynamic-rows/cells/thumbnail',
                            'dataType' => Text::NAME,
                            'dataScope' => 'thumbnail',
                            'fit' => true,
                            'label' => __('Thumbnail'),
                            'sortOrder' => 10,
                        ],
                    ],
                ],
            ],
            'name' => $this->getTextColumn('name', false, __('Name'), 20),
            'status' => $this->getTextColumn('status', true, __('Status'), 30),
            'attribute_set' => $this->getTextColumn('attribute_set', false, __('Attribute Set'), 40),
            'sku' => $this->getTextColumn('sku', true, __('SKU'), 50),
            'price' => $this->getTextColumn('price', true, __('Price'), 60),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Number::NAME,
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 80,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param        $dataScope
     * @param        $fit
     * @param Phrase $label
     * @param        $sortOrder
     *
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
