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
namespace BelVG\ProductTabs\Block;

/**
 * Class Tabs
 * @package BelVG\ProductTabs\Block
 */
class Tabs extends \Magento\Framework\View\Element\Template
{
    /**
     * @var
     */
    protected $tabs;

    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory
     */
    protected $tabCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var
     */
    protected $currentProduct;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \BelVG\ProductTabs\Helper\Config
     */
    protected $productTabsConfigHelper;

    /**
     * Tabs constructor.
     * @param \BelVG\ProductTabs\Helper\Config $productTabsConfigHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Magento\Framework\Registry $registry
     * @param \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $tabCollectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \BelVG\ProductTabs\Helper\Config $productTabsConfigHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Framework\Registry $registry,
        \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $tabCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->productTabsConfigHelper = $productTabsConfigHelper;
        $this->customerSession = $customerSession;
        $this->jsonSerializer = $jsonSerializer;
        $this->registry = $registry;
        $this->tabCollectionFactory = $tabCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \BelVG\ProductTabs\Model\ResourceModel\Tab\Collection
     */
    protected function getTabCollection(): \BelVG\ProductTabs\Model\ResourceModel\Tab\Collection
    {
        /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab\Collection $collection */
        $collection = $this->tabCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->addEntityFilter($this->getProductId());

        return $collection;
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {
        /** @var \BelVG\ProductTabs\Model\Tab $tab */
        foreach ($this->getTabCollection() as $tab) {
            $this->addTab($tab);
        }

        return parent::_prepareLayout();
    }

    /**
     * @param \BelVG\ProductTabs\Model\Tab $tabModel
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addTab(\BelVG\ProductTabs\Model\Tab $tabModel)
    {
        $alias = $tabModel->getData('alias');
        $title = $tabModel->getData('title');
        $block = $tabModel->getData('block_type_class') ?? false;
        $template = $tabModel->getData('block_type_template') ?? false;
        $attributes = $tabModel->getData();

        if (!$title || ($block && $block !== 'BelVG\ProductTabs\Block\Tab\Html' && !$template)) {
            return false;
        }

        if (!$block) {
            $block = $this->getLayout()->getBlock($alias);
            if (!$block) {
                return false;
            }
        } else {
            if ($attributes['block_arguments'] ?? false) {
                $args = explode(',', $attributes['block_arguments']);
                unset($attributes['block_arguments']);
                foreach ($args as $arg) {
                    $arg = explode(':', $arg);
                    $attributes[$arg[0]] = $arg[1];
                }
            }
            $block = $this->getLayout()
                ->createBlock($block, $alias, ['data' => $attributes])
                ->setTemplate($template);
        }

        $tab = array(
            'alias' => $alias,
            'title' => $title
        );

        if (isset($attributes['sort_order'])) {
            $tab['sort_order'] = $attributes['sort_order'];
        }

        $this->tabs[] = $tab;

        $this->setChild($alias, $block);

        //Delete block
        if (($blockName = $tabModel->getData('block_name_for_deleting')) !== '') {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                $this->getLayout()->unsetElement($blockName);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getTabs()
    {
        usort($this->tabs, array($this, 'sort'));

        return $this->tabs;
    }

    /**
     * @param $tab1
     * @param $tab2
     * @return int
     */
    protected function sort($tab1, $tab2)
    {
        if (!isset($tab2['sort_order'])) {
            return -1;
        }

        if (!isset($tab1['sort_order'])) {
            return 1;
        }

        if ($tab1['sort_order'] === $tab2['sort_order']) {
            return 0;
        }

        return ($tab1['sort_order'] < $tab2['sort_order']) ? -1 : 1;
    }

    /**
     * @param $content
     * @return bool
     */
    public function isEmptyContent($content)
    {
        $content = strip_tags(
            $content,
            '<hr><img><iframe><embed><object><video><audio><input><textarea><script><style><link><meta>'
        );
        $content = trim($content);
        return strlen($content) === 0;
    }

    /**
     * @return string
     */
    public function getDisplayType(): string
    {
        return $this->productTabsConfigHelper->getDisplayType();
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->productTabsConfigHelper->isEnabled();
    }

    /**
     * @return bool
     */
    public function isOpenAll(): bool
    {
        return $this->productTabsConfigHelper->isAllOpen();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getProduct()
    {
        if (is_null($this->currentProduct)) {
            $this->currentProduct = $this->registry->registry('product');

            if (!$this->currentProduct->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->currentProduct;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getProductId()
    {
        return $this->getProduct()->getId();
    }
}
