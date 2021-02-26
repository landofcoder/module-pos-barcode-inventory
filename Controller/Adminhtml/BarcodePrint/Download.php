<?php
namespace Lof\BarcodeInventory\Controller\Adminhtml\BarcodePrint;

class Download extends \Magento\Framework\App\Action\Action
{
    protected $fileFactory;
    protected $csvProcessor;
    protected $directoryList;
    protected $resultPageFactory;
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $Resource
    ) {
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->resultPageFactory = $resultPageFactory;
        $this->_resource = $Resource;
        $this->directory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);

        parent::__construct($context);
    }

    public function execute()
    {
        $fileName = "landofcoder_barcode_example.csv";
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
            . "/" . $fileName;
        $this->directory->create('export');
        $stream = $this->directory->openFile($filePath, 'w+');
        $stream->lock();

        $columns = ['sku','qty'];
        foreach ($columns as $column) {
            $header[] = $column;
        }

        $stream->writeCsv($header);
        $itemData = [];
        $itemData[] = "24-MB01";
        $itemData[] = "5";
        $stream->writeCsv($itemData);

        $content = [];
        $content['type'] = 'filename';
        $content['value'] = $filePath;
        $content['rm'] = '1';
        return $this->fileFactory->create($fileName, $content, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
    }
//    protected function _isAllowed()
//    {
//        return $this->_authorization->isAllowed('Lof_BarcodeInventory::Download');
//    }
}
