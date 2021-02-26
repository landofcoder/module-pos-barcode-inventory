<?php
namespace Lof\BarcodeInventory\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template;
use Magento\Framework\UrlInterface;

class PrintPaper extends Template
{

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }
    public function getAssetUrl($asset)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $assetRepository = $objectManager->get('Magento\Framework\View\Asset\Repository');
        return $assetRepository->createAsset($asset)->getUrl();
    }
    public function getDomain()
    {
        return $this->urlBuilder->getBaseUrl();
    }
    public function getFoo()
    {
        return $this->_coreRegistry->registry('foo');
    }
}
