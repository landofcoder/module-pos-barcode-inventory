<?php
namespace Lof\BarcodeInventory\Block\Adminhtml\Barcode;

use Magento\Backend\Block\Template\Context;

class MassPrint extends \Magento\Backend\Block\Template
{
    protected $_coreRegistry;

    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    public function getAssetUrl($asset)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $assetRepository = $objectManager->get('Magento\Framework\View\Asset\Repository');
        return $assetRepository->createAsset($asset)->getUrl();
    }
    public function getFoo()
    {
        // will return 'bar'
        return $this->_coreRegistry->registry('foo');
    }
}
