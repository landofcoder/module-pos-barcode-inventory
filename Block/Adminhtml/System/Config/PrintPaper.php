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

    /**
     * @param $asset
     * @return mixed
     */
    public function getAssetUrl($asset)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $assetRepository = $objectManager->get('Magento\Framework\View\Asset\Repository');
        return $assetRepository->createAsset($asset)->getUrl();
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->urlBuilder->getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getFoo()
    {
        return $this->_coreRegistry->registry('foo');
    }
}
