<?php

namespace BelVG\ProductTabs\Controller\Adminhtml\Tab;

/**
 * Class InlineEdit
 *
 * @package BelVG\ProductTabs\Controller\Adminhtml\Tab
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    protected $tabModelFactory;

    protected $jsonFactory;

    public function __construct(
        \BelVG\ProductTabs\Model\TabFactory $tabModelFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->tabModelFactory = $tabModelFactory;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $tabItems = $this->getRequest()->getParam('items', []);
            if (!count($tabItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($tabItems) as $tabId) {
                    /** @var \BelVG\ProductTabs\Model\Tab $tabModel */
                    $tabModel = $this->tabModelFactory->create();
                    $tabModel->load($tabId);
                    try {
                        $tabModel->setData(array_merge($tabModel->getData(), $tabItems[$tabId]));
                        $tabModel->save();
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithTabId(
                            $tabModel,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    protected function getErrorWithTabId(\BelVG\ProductTabs\Model\Tab $tab, $errorText): string
    {
        return '[Tab ID: ' . $tab->getId() . '] ' . $errorText;
    }
}
