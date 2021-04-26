<?php

namespace Lof\BarcodeInventory\Block\Adminhtml\Barcode;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

class MassPrint extends \Magento\Backend\Block\Template
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    public function getCollection()
    {
        return $this->_coreRegistry->registry('collection');
    }
}
