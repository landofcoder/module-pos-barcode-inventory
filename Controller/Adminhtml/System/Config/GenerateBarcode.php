<?php

namespace Lof\BarcodeInventory\Controller\Adminhtml\System\Config;

use Lof\BarcodeInventory\Helper\Data;

class GenerateBarcode extends \Magento\Backend\App\Action
{
    protected $productCollectionFactory;
    protected $helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Lof\BarcodeInventory\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function execute()
    {
        $productCollection = $this->productCollectionFactory->create();
        $this->helper->generateBarcode($productCollection);
    }
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_BarcodeInventory::generate_barcode');
    }
}