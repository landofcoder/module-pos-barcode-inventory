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

class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * Download constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\ResourceConnection $Resource
     * @throws \Magento\Framework\Exception\FileSystemException
     */
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

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $fileName = "landofcoder_barcode_example.csv";
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
            . "/" . $fileName;
        $this->directory->create('export');
        $stream = $this->directory->openFile($filePath, 'w+');
        $stream->lock();

        $columns = ['sku', 'qty'];
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
