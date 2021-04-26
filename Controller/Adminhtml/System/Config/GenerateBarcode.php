<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_BarcodeInventory
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

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
