<?php

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
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_BarcodeInventory::upload_csv');
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
}
