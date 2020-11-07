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
namespace BelVG\ProductTabs\Controller\Adminhtml\Tab;

/**
 * Class MassDelete
 *
 * @package BelVG\ProductTabs\Controller\Adminhtml\Tab
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'BelVG_ProductTabs::tab_delete';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory
     */
    protected $tabCollectionFactory;

    /**
     * @var \BelVG\ProductTabs\Model\ResourceModel\TabFactory
     */
    protected $tabResourceModelFactory;

    /**
     * MassDelete constructor.
     * @param \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory
     * @param \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $tabCollectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     */
    public function __construct(
        \BelVG\ProductTabs\Model\ResourceModel\TabFactory $tabResourceModelFactory,
        \BelVG\ProductTabs\Model\ResourceModel\Tab\CollectionFactory $tabCollectionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        $this->tabCollectionFactory = $tabCollectionFactory;
        $this->tabResourceModelFactory = $tabResourceModelFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab\Collection $tabCollection */
        $tabCollection = $this->filter->getCollection($this->tabCollectionFactory->create());
        $collectionSize = $tabCollection->getSize();

        /** @var \BelVG\ProductTabs\Model\ResourceModel\Tab $tabResourceModel */
        $tabResourceModel = $this->tabResourceModelFactory->create();

        foreach ($tabCollection as $tab) {
            $tabResourceModel->delete($tab);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        return $this->resultRedirectFactory->create()->setPath('*/index/grid');
    }
}
