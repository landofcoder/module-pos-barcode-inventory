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

namespace Lof\BarcodeInventory\Model;

use Lof\BarcodeInventory\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\QuoteFactory;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Psr\Log\LoggerInterface;


class GenerateBarcodeManagement implements \Lof\BarcodeInventory\Api\GenerateBarcodeManagementInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ObjectManagerInterface
     */
    private $_objectManager;

    /**
     * @var BarcodeGeneratorPNG
     */
    private $generator;

    /**
     * @var ProductFactory
     */
    private $product;

    /**
     * @var Manager
     */
    private $_moduleManager;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var mixed|LoggerInterface|null
     */
    private $logger;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * GenerateBarcodeManagement constructor.
     * @param Data $helper
     * @param BarcodeGeneratorPNG $generator
     * @param FormKey $formKey
     * @param Cart $cart
     * @param ProductFactory $product
     * @param StockRegistryInterface $stockRegistry
     * @param Manager $moduleManager
     * @param ObjectManagerInterface $objectmanager
     * @param CollectionFactory $productCollectionFactory
     * @param QuoteFactory $quoteFactory
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Data $helper,
        BarcodeGeneratorPNG $generator,
        FormKey $formKey,
        Cart $cart,
        ProductFactory $product,
        StockRegistryInterface $stockRegistry,
        Manager $moduleManager,
        ObjectManagerInterface $objectmanager,
        CollectionFactory $productCollectionFactory,
        QuoteFactory $quoteFactory,
        ProductRepositoryInterface $productRepository,
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
        $this->productRepository = $productRepository;
        $this->logger = $logger ?: \Magento\Framework\App\ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getGenerateBarcode()
    {
        $productCollection = $this->productCollectionFactory->create();
        $this->helper->generateBarcode($productCollection);
        return "Barcodes generated!";
    }

    /**
     * {@inheritdoc}
     */
    public function getProductInfo($barcode, $store_id = null)
    {
        $productSku = $this->productCollectionFactory->create()->addFieldToFilter('sku', $barcode)
                        ->addStoreFilter($store_id)
                        ->getFirstItem();

        $productByBarcode = $this->product->create();
        if($store_id && is_numeric($store_id)){
            $productByBarcode->setStoreId($store_id);
        }
        $productByBarcode = $productByBarcode->loadByAttribute('barcode', $barcode);
        $productData = [];
        if ($productSku->getId()) {
            $productBySku = $this->productRepository->get($barcode);
            $productData = $productBySku->getData();
            $productData['qty'] = 1;
            $productData['model'] = $productBySku;
        } elseif ($productByBarcode) {
            $productData = $productByBarcode->getData();
            $productData['qty'] = 1;
            $productData['model'] = $productByBarcode;
        } elseif ($this->_moduleManager->isEnabled('Lof_MultiBarcode')) {
            $multiQtyBarcode = $this->_objectManager->create("Lof\MultiBarcode\Model\ResourceModel\Barcode\Collection")
                ->addFieldToFilter('barcode', $barcode)->getFirstItem();
            if ($multiQtyBarcode->getData()) {
                $productId = $multiQtyBarcode->getData('product_id');
                $product = $this->product->create()->load($productId);
                $productData = $product->getData();
                $productData['qty'] = $multiQtyBarcode->getQty();
                $productData['model'] = $productData;

            }
        }
        return $productData;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllBarcode($pageSize, $pageNumber)
    {
        $key = 0;
        $lastPage = 0;
        $list = [];
        if ($this->_moduleManager->isEnabled('Lof_MultiBarcode')) {
            $multiQtyCollection = $this->_objectManager->create("Lof\MultiBarcode\Model\ResourceModel\Barcode\Collection")
                ->setPageSize($pageSize)
                ->setCurPage($pageNumber);
            foreach ($multiQtyCollection as $item) {
                $barcode = $item->getBarcode();
                $productId = $item->getProductId();
                $qty = $item->getQty();
                $whCode = $item->getWarehouseCode();
                $source = $item->getSource();
                $obj = [
                    "barcode" => $barcode,
                    "product_id" => $productId,
                    "qty" => $qty,
                    "warehouse_code" => $whCode,
                    "source_code" => $source
                ];
                $list[$key] = $obj;
                $key++;
            }
            $lastPage += $multiQtyCollection->getLastPageNumber();
        }
        $collection = $this->productCollectionFactory->create()->addAttributeToSelect('barcode')->addFieldToFilter(
            'barcode',
            ['neq' => 'NULL']
        )->setPageSize($pageSize)->setCurPage($pageNumber);
        foreach ($collection as $product) {
            $barcode = $product->getBarcode();
            if ($barcode) {
                $productId = $product->getId();
                $qty = "1";
                $obj = [
                    "barcode" => $barcode,
                    "product_id" => $productId,
                    "qty" => $qty,
                    "warehouse_code" => null,
                    "source_code" => null
                ];
                $list[$key] = $obj;
                $key++;
            }
        }
        if (!isset($list)) {
            $list = "";
        }
        $lastPage += $collection->getLastPageNumber();
        return ['total_page' => $lastPage, 'items' => $list];
    }

    /**
     * {@inheritdoc}
     */
    public function addProductToCartByBarcode($barcode, $cartId, $store_id = null)
    {
        $product = $this->getProductInfo($barcode, $store_id);

        if (isset($product) && $product) {
            $params = [
                'product' => $product['entity_id'],
                'items_qty' => $product['qty']
            ];
            try {
                $cart = $this->cart;
                if ($cartId) {
                    $cart->getQuote()->load($cartId);
                }
                $cart->addProduct($product['model'], $params);
                $cart->save();
            } catch (LocalizedException $e) {
                $this->logger->critical($e->getMessage());
            }
            return ['code' => 0, 'message' => __("Add product to cart successful.")];
            ;
        } else {
            return ['code' => 1, 'message' => __("Barcode does not exists.")];
        }
    }

    /**
     * @param string $barcode
     * @return mixed|string
     * @throws NoSuchEntityException
     */
    public function updateQtyByBarcode($barcode)
    {
        //cai nay lam sau multi warehouse
        $productByBarcode = $this->productCollectionFactory->create()->addFieldToFilter(
            'barcode',
            $barcode
        )->getFirstItem();
        $productBySku = $this->productCollectionFactory->create()->addFieldToFilter('sku', $barcode)->getFirstItem();
        if ($productByBarcode->getData()) {
            $sku = $productByBarcode->getSku();
            $stockItem = $this->stockRegistry->getStockItemBySku($sku);
            $stockItem->setQty($stockItem->getQty() + 1);
            $stockItem->setIsInStock((bool)1);
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
        } elseif ($productBySku->getData()) {
            $sku = $barcode;
            $stockItem = $this->stockRegistry->getStockItemBySku($sku);
            $stockItem->setQty($stockItem->getQty() + 1);
            $stockItem->setIsInStock((bool)1);
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
        } elseif ($this->_moduleManager->isEnabled('Lof_MultiBarcode')) {
            $object = $this->_objectManager;
            $multiQtyBarcode = $object->create("Lof\MultiBarcode\Model\ResourceModel\Barcode\Collection")->addFieldToFilter(
                'barcode',
                $barcode
            )->getFirstItem();
            if ($multiQtyBarcode->getData()) {
                $productId = $multiQtyBarcode->getProductId();
                $product = $this->product->create()->load($productId);
                $qty = $multiQtyBarcode->getQty();
                if ($multiQtyBarcode->getSource()) {
                    $sourceItem = $this->_objectManager->create("Magento\Inventory\Model\ResourceModel\SourceItem\Collection")
                        ->addFieldToFilter('source_code', $multiQtyBarcode->getSource())->addFieldToFilter(
                            'sku',
                            $product->getSku()
                        )->getFirstItem();
                    if ($sourceItem->getData()) {
                        $sourceItem->setQuantity($qty + $sourceItem->getQuantity());
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
                    $stock = $this->_objectManager->create("Lof\Inventory\Model\ResourceModel\Stock\Collection")->addFieldToFilter(
                        "warehouse_code",
                        $multiQtyBarcode->getWarehouseCode()
                    )
                        ->addFieldToFilter("product_sku", $product->getSku())->getFirstItem();
                    if ($stock->getData()) {
                        $stock->setTotalQty($stock->getTotalQty() + $qty)->setUpdatedAt($date);
                        $stock->save();
                    } else {
                        $stock = $this->_objectManager->create("Lof\Inventory\Model\Stock");
                        $stock->setWarehouseCode($multiQtyBarcode->getWarehouseCode())->setProductSku($product->getSku())
                            ->setTotalQty($qty)->setCreatedAt($date)->setUpdatedAt($date);
                    }
                } else {
                    $sku = $product->getSku();
                    $stockItem = $this->stockRegistry->getStockItemBySku($sku);
                    $stockItem->setQty($stockItem->getQty() + $multiQtyBarcode->getQty());
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
