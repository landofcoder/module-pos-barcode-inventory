<?php
namespace Lof\BarcodeInventory\Api;

interface GenerateBarcodeManagementInterface
{

    /**
     * GET for generateBarcode api
     * @return string
     */
    public function getGenerateBarcode();

    /**
     * GET product info by barcode
     * @param string $barcode
     * @return mixed
     */
    public function getProductInfo($barcode);

    /**
     * GET all barcode with product_id, qty
     * @param integer $pageSize
     * @param integer $pageNumber
     * @return mixed
     */
    public function getAllBarcode($pageSize, $pageNumber);

    /**
     * GET all barcode with product_id, qty
     * @param string $barcode
     * @param string $cartId
     * @return mixed
     */
    public function addProductToCartByBarcode($barcode, $cartId);

    /**
     * GET all barcode with product_id, qty
     * @param string $barcode
     * @return mixed
     */
    public function updateQtyByBarcode($barcode);
}
