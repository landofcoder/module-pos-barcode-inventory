<?php

namespace Lof\BarcodeInventory\Controller\Adminhtml\Barcode;

use Lof\BarcodeInventory\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 */
class MassPrint extends \Magento\Backend\App\Action implements HttpPostActionInterface
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var
     */
    protected $_helper;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Registry
     */
    protected $_coreRegistry;
    /**
     * @var PageFactory
     */
    protected $_pageFactory;
    /**
     * @var ResponseFactory
     */
    protected $_responseFactory;
    /**
     * @var Data
     */
    private $helper;


    /**
     * MassPrint constructor.
     * @param Context $context
     * @param Filter $filter
     * @param Registry $coreRegistry
     * @param CollectionFactory $collectionFactory
     * @param ResponseFactory $responseFactory
     * @param Data $helper
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Registry $coreRegistry,
        CollectionFactory $collectionFactory,
        ResponseFactory $responseFactory,
        Data $helper,
        PageFactory $pageFactory
    ) {
        $this->filter = $filter;
        $this->helper = $helper;
        $this->_coreRegistry = $coreRegistry;
        $this->_responseFactory = $responseFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_BarcodeInventory::mass_print');
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $this->_coreRegistry->register('collection', $collection);
        $this->messageManager->addSuccessMessage(__('A total of %1 barcode(s) have been printed.', $collectionSize));
        $resultPage = $this->_pageFactory->create();
        $resultPage->addHandle('print');
        return $resultPage;
        return $this->_pageFactory->create();
    }
}
