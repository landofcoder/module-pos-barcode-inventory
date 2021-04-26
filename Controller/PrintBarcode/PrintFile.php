<?php
namespace Lof\BarcodeInventory\Controller\PrintBarcode;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Lof\BarcodeInventory\Helper\Data;

class PrintFile extends Action
{
    /**
     * @var
     */
    public $imageUploader;

    /**
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * PrintFile constructor.
     * @param Context $context
     * @param Data $data
     * @param Csv $csvProcessor
     * @param CollectionFactory $productCollectionFactory
     * @param Filesystem $filesystem
     * @param PageFactory $pageFactory
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        Data $data,
        Csv $csvProcessor,
        CollectionFactory $productCollectionFactory,
        Filesystem $filesystem,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->_helper = $data;
        $this->csvProcessor = $csvProcessor;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|Raw|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        /** @var Raw $response */
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
