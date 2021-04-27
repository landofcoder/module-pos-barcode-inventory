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

namespace Lof\BarcodeInventory\Controller\Adminhtml\BarcodePrint;

use Magento\Framework\Controller\ResultFactory;

class UploadCsv extends \Magento\Backend\App\Action
{
    /**
     * @var \Lof\BarcodeInventory\Model\FileUploader
     */
    public $imageUploader;

    /**
     * @var
     */
    protected $csvProcessor;

    /**
     * @var
     */
    protected $productCollectionFactory;

    /**
     * @var \Lof\BarcodeInventory\Helper\Data
     */
    protected $_helper;

    /**
     * UploadCsv constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Lof\BarcodeInventory\Model\FileUploader $imageUploader
     * @param \Lof\BarcodeInventory\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Lof\BarcodeInventory\Model\FileUploader $imageUploader,
        \Lof\BarcodeInventory\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
        $this->_helper = $helperData;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $tmpUploadPath = $this->_helper->getPrintConfig("tmp_path");
            $basePath = $this->_helper->getPrintConfig("base_path");
            $this->imageUploader->setBaseTmpPath($tmpUploadPath);
            $this->imageUploader->setBasePath($basePath);
            $result = $this->imageUploader->saveFileToTmpDir('import');
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_BarcodeInventory::upload_csv');
    }
}
