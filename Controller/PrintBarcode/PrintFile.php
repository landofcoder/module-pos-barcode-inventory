<?php
namespace Lof\BarcodeInventory\Controller\PrintBarcode;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Lof\BarcodeInventory\Helper\Data;

class PrintFile extends Action
{
    public $imageUploader;
    protected $csvProcessor;
    protected $productCollectionFactory;
    protected $_helper;
    private $mediaDirectory;

    public function __construct(
        Context $context,
        Data $data,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->_helper = $data;
        $this->csvProcessor = $csvProcessor;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');
        $file = $this->getRequest()->getParam("file");
        if ($file) {
            $result['file'] = $file;
            $tmpUploadPath = $this->_helper->getPrintConfig("tmp_path");
            $tmpUploadPath = $this->mediaDirectory->getAbsolutePath($tmpUploadPath);
            $csvUploadFilePath =  $tmpUploadPath."/".$result['file'];
            if (file_exists($csvUploadFilePath)) {
                $csvData = $this->csvProcessor->getData($csvUploadFilePath);
                $html = '';
                foreach ($csvData as $row) {
                    if ($row[0] != 'sku' && $row[1] != 'qty') {
                        $html .= $this->_helper->printPdf($row[0], $row[1]);
                    }
                }
                $css = $this->_helper->getCss();
                $html .= "<style type='text/css'>$css</style>";
                $response->setContents(
                    json_encode(
                        [
                            'data1' => $html,
                        ]
                    )
                );
                return $response;
            } else {
                return $response;
            }
        } else {
            return $response;
        }
    }
}
