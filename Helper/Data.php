<?php

namespace Lof\BarcodeInventory\Helper;

use Magento\Catalog\Model\ProductFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * Class Data
 * @package Lof\BarcodeInventory\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var File
     */
    private $file;
    /**
     * @var Filesystem\DirectoryList
     */
    private $dir;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;
    /**
     * @var FileFactory
     */
    protected $fileFactory;
    /**
     * @var UploaderFactory $fileUploader
     */
    protected $fileUploader;
    /**
     * @var Filesystem $filesystem
     */
    protected $filesystem;
    /**
     *
     */
    const BARCODE = 'barcode/';
    /**
     *
     */
    const BARCODELABEL = 'barcode_label/';
    /**
     * @var ProductFactory
     */
    protected $product;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var BarcodeGeneratorPNG
     */
    private $barcodeGeneratorPNG;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;


    /**
     * Data constructor.
     *
     * @param File $file
     * @param FileFactory $fileFactory
     * @param Filesystem\DirectoryList $dir
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param UrlInterface $urlBuilder
     * @param ProductFactory $product
     * @param UploaderFactory $fileUploader $fileFactory
     * @param StoreManagerInterface $storeManager
     * @param BarcodeGeneratorPNG $barcodeGeneratorPNG
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @throws FileSystemException
     */
    public function __construct(
        File $file,
        FileFactory $fileFactory,
        Filesystem\DirectoryList $dir,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        UrlInterface $urlBuilder,
        ProductFactory $product,
        UploaderFactory $fileUploader,
        StoreManagerInterface $storeManager,
        BarcodeGeneratorPNG $barcodeGeneratorPNG,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->filesystem = $filesystem;
        $this->urlBuilder = $urlBuilder;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->file = $file;
        $this->dir = $dir;
        $this->fileFactory = $fileFactory;
        $this->scopeConfig = $scopeConfig;
        $this->fileUploader = $fileUploader;
        $this->product = $product;
        $this->storeManager = $storeManager;
        $this->barcodeGeneratorPNG = $barcodeGeneratorPNG;
        $this->priceHelper  = $priceHelper;
    }

    /**
     * @return mixed|string
     */
    public function getCss()
    {
        $objectManager = ObjectManager::getInstance();
        $label_id = $this->getDesignConfig('select_label');
        if ($label_id && $this->getDesignConfig('use_label') == "1") {
            $css = $this->getDesignConfig('barcode_label_css');
            $label = $objectManager->create('Lof\BarcodeLabel\Model\Label')->load($label_id);
            $width = $label->getLabelWidth();
            $height = $label->getLabelHeight();
            $font = $label->getFontSize();
            $margin = $label->getMarginTop() . " " . $label->getMarginRight() . " " . $label->getMarginBottom() . " " . $label->getMarginLeft();
            return "$css .barcode_paper {width:$width; height: $height; margin: $margin; font-size: $font }";
        } else {
            return $this->getDesignConfig('barcode_label_css');
        }
    }

    /**
     * @param $productCollection
     * @return string
     */
    public function generatePaperPrint($productCollection)
    {
        $results = '';
        $objectManager = ObjectManager::getInstance();
        $label_id = $this->getDesignConfig('select_label');
        $label = $objectManager->create('Lof\BarcodeLabel\Model\Label');
        $att = $label->load($label_id)->getProductAttribute();
        $productCollection->addAttributeToSelect('barcode');
        $productCollection->addAttributeToSelect('name');
        $productCollection->addAttributeToSelect('price');
        $productCollection->addAttributeToSelect('status');
        $productCollection->addAttributeToSelect('final_price');
        $attributes = explode(',', $att);
        foreach ($attributes as $item) {
            $productCollection->addAttributeToSelect($item);
        }
        foreach ($productCollection as $product) {
            $str = $this->generateLabel($product);
            $str = "<div class='barcode_paper'>$str</div>";
            $results .= $str;
        }
        return $results;
    }

    /**
     * @param $productSku
     * @param $qty
     * @return string
     */
    public function printPdf($productSku, $qty)
    {
        $product = $this->product->create();
        $product->load($product->getIdBySku($productSku));
        $str = $this->generateLabel($product);
        $return = '';
        for ($i = 0; $i < $qty; $i++) {
            $return .= $str;
        }
        return $return;
    }

    /**
     * @param $productCollection
     */
    public function generateBarcode($productCollection)
    {
        try {
            $collection = $productCollection->addAttributeToSelect('*')
                ->load();
            foreach ($collection as $product) {
                if (!$product->getBarcode()) {
                    $value = $product->getId() . "-" . $this->generateRandomString(8);
                    $product->addAttributeUpdate('barcode', $value, 0);
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::BARCODE . 'general/' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getDesignConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::BARCODE . 'design/' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getPrintConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::BARCODE . 'print_setting/' . $code, $storeId);
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param $code
     * @return string
     */
    public function getBase64Barcode($code){
        return base64_encode($this->barcodeGeneratorPNG->getBarcode($code, $this->barcodeGeneratorPNG::TYPE_CODE_128));
    }

    /**
     * @param $price
     * @return float|string
     */
    public function formatPrice($price){
        return $this->priceHelper->currency($price,true,false);
    }

    /**
     * @param $product
     * @return string|string[]
     */
    public function generateLabel($product){
        if ($this->getGeneralConfig('attribute_barcode') == 'sku') {
            $code = $product->getSku();
        } else {
            $code = $product->getBarcode();
        }
        $str = '';
        if ($code) {
            if (!$this->getDesignConfig('use_label')) {
                $str = $this->getDesignConfig('barcode_label_content');
            } else {
                $objectManager = ObjectManager::getInstance();
                $label_id = $this->getDesignConfig('select_label');
                $label = $objectManager->create('Lof\BarcodeLabel\Model\Label');
                $arr = $label->load($label_id)->getProductAttribute();
                if ($this->getDesignConfig('display_logo')) {
                    $width = $this->getDesignConfig('logo_width');
                    $height = $this->getDesignConfig('logo_height');
                    $url = str_replace("/index.php/", "/", $this->urlBuilder->getBaseUrl());
                    $logo = $this->getDesignConfig('logo');
                    $img = $url . "media/lof/barcode_logo/" . $logo;
                    $str .= "<div class = 'row'><img width='$width' height='$height' src=" . $img . " alt='logo' /></div>";
                }
                $str .= "<div class = 'row'><b>{{product_name}}</b></div>";
                $str .= "<div class = 'row'><b>{{barcode}}</b></div>";
                $str .= "<div class = 'row'><b>{{barcode_number}}</b></div>";
                $str .= "<div class = 'row'><b>{{product_price}}</b></div>";
                $attributes = explode(',', $arr);
                foreach ($attributes as $item) {
                    if ($product->getAttributeText('barcode')) {
                        $att = $product->getAttributeText('barcode');
                    } else {
                        $att = $product->getData($item);
                    }
                    if (!$att) {
                        continue;
                    }
                    $label = $product->getResource()->getAttribute($item)->getFrontendLabel();
                    $str .= "<div class = 'row'><b>$label: $att</b></div>";
                }
                $str = "<div class='barcode_paper'>$str</div>";
            }
            $bar = "<img width = '100%' src='data:image/png;base64," . $this->getBase64Barcode($code) . '\'>';
            $str = str_replace("{{product_name}}", $product->getName(), $str);
            $str = str_replace("{{barcode}}", $bar, $str);
            $str = str_replace("{{product_sku}}", $product->getSku(), $str);
            $str = str_replace("{{product_price}}", "Price: " . $this->formatPrice($product->getFinalPrice()), $str);
            $str = str_replace("{{barcode_number}}", $code, $str);
        }
        return $str;
    }
}
