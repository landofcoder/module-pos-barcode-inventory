<?php

namespace Lof\BarcodeInventory\Controller\Adminhtml\System\Config;

use Lof\BarcodeInventory\Helper\Data;

class GenerateBarcode extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * GenerateBarcode constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param Data $helper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Lof\BarcodeInventory\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $productCollection = $this->productCollectionFactory->create();
        $this->helper->generateBarcode($productCollection);
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_BarcodeInventory::generate_barcode');
    }
}
