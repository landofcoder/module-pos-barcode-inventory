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
