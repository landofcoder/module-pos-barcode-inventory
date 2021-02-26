<?php
namespace Lof\BarcodeInventory\Controller\Adminhtml\System\Config;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class PrintPaper extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        return $this->_pageFactory->create();
    }
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_BarcodeInventory::print_paper');
    }
}
