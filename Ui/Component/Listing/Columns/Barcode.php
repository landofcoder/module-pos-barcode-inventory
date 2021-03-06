<?php
namespace Lof\BarcodeInventory\Ui\Component\Listing\Columns;

use Magento\Catalog\Helper\Image;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * Class Barcode
 * @package Lof\BarcodeInventory\Ui\Component\Listing\Columns
 */
class Barcode extends Column
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Image
     */
    private $imageHelper;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var BarcodeGeneratorPNG
     */
    private $barcodeGenerator;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Image $imageHelper
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param BarcodeGeneratorPNG $barcodeGeneratorPNG
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Image $imageHelper,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        BarcodeGeneratorPNG $barcodeGeneratorPNG,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->barcodeGenerator = $barcodeGeneratorPNG;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $url = 'data:image/png;base64,' . base64_encode($this->barcodeGenerator->getBarcode($item[$fieldName], $this->barcodeGenerator::TYPE_CODE_128));
                    $item[$fieldName . '_src'] = $url;
                    $item[$fieldName . '_alt'] = $this->getAlt($item) ?: '';
                    $item[$fieldName . '_orig_src'] = $url;
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
