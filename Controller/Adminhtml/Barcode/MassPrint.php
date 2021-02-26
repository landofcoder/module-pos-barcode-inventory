<?php
namespace Lof\BarcodeInventory\Controller\Adminhtml\Barcode;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
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

    protected $_helper;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    protected $_coreRegistry;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;
    protected $_responseFactory;


    public function __construct(
        Context $context,
        Filter $filter,
        Registry $coreRegistry,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Lof\BarcodeInventory\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->filter = $filter;
        $this->helper = $helper;
        $this->_coreRegistry = $coreRegistry;
        $this->_responseFactory = $responseFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_BarcodeInventory::mass_print');
    }
    public function execute()
    {


        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $this->_coreRegistry->register('foo', $collection);

        $this->messageManager->addSuccessMessage(__('A total of %1 barcode(s) have been printed.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $this->_pageFactory->create();
    }
}
