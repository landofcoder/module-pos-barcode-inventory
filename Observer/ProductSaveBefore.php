<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_BarcodeInventory
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\BarcodeInventory\Observer;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

/**
 * Lof BarcodeInventory ProductSaveBefore Observer.
 */
class ProductSaveBefore implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductFactory
     */
    protected $product;

    protected $url;

    /**
     * @param RequestInterface $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param UrlInterface $url
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        UrlInterface $url
    ) {
        $this->_messageManager = $messageManager;
        $this->request=$request;
        $this->url = $url;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $product = $observer->getProduct();
            $productId = $observer->getProduct()->getId();
            $barcode = trim($this->request->getPost('product')['barcode']);
            $collection = $product->getCollection()->addFieldToFilter("barcode", $barcode);
            $barcode_leng = strlen($barcode);
            $exist_product_id = 0;
            if ($collection->getSize()) {
                $data = $collection->getFirstItem();
                $exist_product_id = $data->getId();
            }
            if (!$exist_product_id || ($exist_product_id && ($exist_product_id == $productId))) {
                if (($barcode_leng > 0 && $barcode_leng < 9) || $barcode_leng > 12) {
                    $this->_messageManager->addError(__("Barcode must be a string at least 9 and no more than 12 characters"));
                    $product->setBarcode("");
                }
            } elseif ($exist_product_id) {
                $this->_messageManager->addError(__("Barcode have already existed"));
                $product->setBarcode("");
            }
        } catch (\Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
    }
}
