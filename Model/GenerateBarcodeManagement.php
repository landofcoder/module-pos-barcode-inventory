<?php

namespace Lof\BarcodeInventory\Model;

use Magento\Framework\Exception\LocalizedException;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Psr\Log\LoggerInterface;

class GenerateBarcodeManagement implements \Lof\BarcodeInventory\Api\GenerateBarcodeManagementInterface
{
    protected $helper;
    private $_objectManager;

    private $collection;
    /**
     * @var BarcodeGeneratorPNG
     */
    private $generator;
    /**
     * @var Magento\Catalog\Model\Product
     */
    private $product;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $_moduleManager;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;
    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    private $stockItem;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    public function __construct(
        \Lof\BarcodeInventory\Helper\Data $helper,
        BarcodeGeneratorPNG $generator,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        LoggerInterface $logger = null
    ) {
        $this->helper = $helper;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->stockRegistry = $stockRegistry;
        $this->product = $product;
        $this->_moduleManager = $moduleManager;
        $this->_objectManager = $objectmanager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->generator = $generator;
        $this->quoteFactory = $quoteFactory;
        $this->logger = $logger ?: \Magento\Framework\App\ObjectManager::getInstance()->get(LoggerInterface::class);
    }
    /**
     * {@inheritdoc}
     */
    public function getGenerateBarcode()
    {
        $this->generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $productCollection = $this->productCollectionFactory->create();
        $this->helper->generateBarcode($productCollection, $this->generator);
        return "Barcodes generated!";
    }
    public function getProductInfo($barcode)
    {
        $productbysku = $this->productCollectionFactory->create()->addFieldToFilter('sku', $barcode);
        $productbybarcode = $this->productCollectionFactory->create()->addFieldToFilter('barcode', $barcode);
        if ($productbysku->getData()) {
            return $productbysku->getData();
        } elseif ($productbybarcode->getData()) {
            return $productbybarcode->getData();
        } elseif ($this->_moduleManager->isEnabled('Lof_MultiBarcode')) {
            $multiQtyBarcode = $this->_objectManager->create("Lof\MultiBarcode\Model\ResourceModel\Barcode\Collection")->addFieldToFilter('barcode', $barcode);
            if ($multiQtyBarcode->getData()) {
                $productId = $multiQtyBarcode->getData()['0']['product_id'];
                $product = $this->product->create()->load($productId);
                return [$product->getData()];
            } else {
                return "Barcode does not exists";
            }
        } else {
            return "Barcode does not exists";
        }
    }
    public function getAllBarcode($pageSize, $pageNumber)
    {
        if ($this->_moduleManager->isEnabled('Lof_MultiBarcode')) {
            $multiQtyCollection = $this->_objectManager->create("Lof\MultiBarcode\Model\ResourceModel\Barcode\Collection")->setPageSize($pageSize)->setCurPage($pageNumber);
            foreach ($multiQtyCollection as $key => $item) {
                $barcode = $item->getBarcode();
                $productId = $item->getProductId();
                $qty =  $item->getQty();
                $whcode =  $item->getWarehouseCode();
                $source =  $item->getSource();
                $obj = ["barcode"=>$barcode, "product_id"=>$productId, "qty"=>$qty, "warehouse_code"=> $whcode, "source_code"=> $source];
                $list[$key] = $obj;
            }
            $lastpage = $multiQtyCollection->getLastPageNumber();
            if (!isset($list)) {
                $list = "";
            }
            return ['data' => array('total_page' => $lastpage, 'list' => $list)];
        } else {
            $collection = $this->productCollectionFactory->create()->addAttributeToSelect('barcode')->addFieldToFilter(
                'barcode',
                ['neq' => 'NULL']
            )->setPageSize($pageSize)->setCurPage($pageNumber);
            $list = [];
            foreach ($collection as $key => $product) {
                $barcode = $product->getBarcode();
                if ($barcode) {
                    $productId = $product->getId();
                    $qty = "1";
                    $obj = ["barcode"=>$barcode, "product_id"=>$productId, "qty"=>$qty, "warehouse_code"=> null, "source_code"=> null];
                    $list[$key] = $obj;
                }
            }
            if (!isset($list)) {
                $list = "";
            }
            $lastpage = $collection->getLastPageNumber();
            return ['data' => array('total_page' => $lastpage, 'list' => $list)];
        }
    }
    public function addProductToCartByBarcode($barcode, $cartId)
    {
        $productbybarcode = $this->productCollectionFactory->create()->addFieldToFilter('barcode', $barcode)->getFirstItem();
        if (!$this->_moduleManager->isEnabled('Lof_MultiBarcode') && $productbybarcode->getData()) {
            $productId = $productbybarcode->getId();
            $qty = 1;
        } elseif ($this->_moduleManager->isEnabled('Lof_MultiBarcode')) {
            $multiQtyBarcode = $this->_objectManager->create("Lof\MultiBarcode\Model\ResourceModel\Barcode\Collection")->addFieldToFilter('barcode', $barcode)->getFirstItem();
            if ($multiQtyBarcode->getData()) {
                $productId = $multiQtyBarcode->getProductId();
                $qty = $multiQtyBarcode->getQty();
            }
        }
        if (isset($productId)) {
            $params = array(
                'product' => $productId,
                'items_qty'   => $qty
            );
            $_product = $this->product->create()->load($productId);
            try {
                $cart = $this->cart;
                if ($cartId != "0") {
                    $cart->getQuote()->load($cartId);
                } 
                $cart->addProduct($_product, $params);
                $cart->save();
            } catch (LocalizedException $e) {
                $this->logger->critical($e->getMessage());
            }
            return $cart->getQuote()->getItemsQty().$cart->getQuote()->getEntityId();
        } else {
            return "Barcode does not exists";
        }
    }
    public function updateQtyByBarcode($barcode)
    {
        $productbybarcode = $this->productCollectionFactory->create()->addFieldToFilter('barcode', $barcode)->getFirstItem();
        $productbysku = $this->productCollectionFactory->create()->addFieldToFilter('sku', $barcode)->getFirstItem();
        if ($productbybarcode->getData()) {
            $sku =$productbybarcode->getSku();
            $stockItem = $this->stockRegistry->getStockItemBySku($sku);
            $stockItem->setQty($stockItem->getQty()+1);
            $stockItem->setIsInStock((bool)1);
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
        } elseif ($productbysku->getData()) {
            $sku = $barcode;
            $stockItem = $this->stockRegistry->getStockItemBySku($sku);
            $stockItem->setQty($stockItem->getQty()+1);
            $stockItem->setIsInStock((bool)1);
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
        } elseif ($this->_moduleManager->isEnabled('Lof_MultiBarcode')) {
            $object = $this->_objectManager;
            $multiQtyBarcode = $object->create("Lof\MultiBarcode\Model\ResourceModel\Barcode\Collection")->addFieldToFilter('barcode', $barcode)->getFirstItem();
            if ($multiQtyBarcode->getData()) {
                $productId = $multiQtyBarcode->getProductId();
                $product = $this->product->create()->load($productId);
                $qty = $multiQtyBarcode->getQty();
                if ($multiQtyBarcode->getSource()) {
                    $sourceItem = $this->_objectManager->create("Magento\Inventory\Model\ResourceModel\SourceItem\Collection")
                        ->addFieldToFilter('source_code', $multiQtyBarcode->getSource())->addFieldToFilter('sku', $product->getSku())->getFirstItem();
                    if ($sourceItem->getData()) {
                        $sourceItem->setQuantity($qty+$sourceItem->getQuantity());
                        $sourceItem->save();
                    } else {
                        $sourceItem = $this->_objectManager->create("Magento\Inventory\Model\SourceItem");
                        $sourceItem->setData("sku", $product->getSku());
                        $sourceItem->setSourceCode($multiQtyBarcode->getSource());
                        $sourceItem->setQuantity($qty);
                        $sourceItem->setStatus('1');
                        $sourceItem->save();
                    }
                } elseif ($multiQtyBarcode->getWarehouseCode()) {
                    $objDate = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
                    $date = $objDate->gmtDate();
                    $stock = $this->_objectManager->create("Lof\Inventory\Model\ResourceModel\Stock\Collection")->addFieldToFilter("warehouse_code", $multiQtyBarcode->getWarehouseCode())
                    ->addFieldToFilter("product_sku", $product->getSku())->getFirstItem();
                    if ($stock->getData()) {
                        $stock->setTotalQty($stock->getTotalQty()+$qty)->setUpdatedAt($date);
                        $stock->save();
                    } else {
                        $stock = $this->_objectManager->create("Lof\Inventory\Model\Stock");
                        $stock->setWarehouseCode($multiQtyBarcode->getWarehouseCode())->setProductSku($product->getSku())
                            ->setTotalQty($qty)->setCreatedAt($date)->setUpdatedAt($date);
                    }
                } else {
                    $sku =$product->getSku();
                    $stockItem = $this->stockRegistry->getStockItemBySku($sku);
                    $stockItem->setQty($stockItem->getQty()+$multiQtyBarcode->getQty());
                    $stockItem->setIsInStock((bool)1);
                    $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
                }
            } else {
                return "Barcode does not exist";
            }
        } else {
            return "Barcode does not exist";
        }
    }
}
