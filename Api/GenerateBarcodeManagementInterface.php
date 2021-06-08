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
use Magento\Store\Model\Store;

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
     * @param null|string|bool|int|Store $store_id
     * @return mixed
     */
    public function getProductInfo($barcode, $store_id = null);

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
     * @param null|string|bool|int|Store $store_id
     * @return mixed
     */
    public function addProductToCartByBarcode($barcode, $cartId, $store_id = null);

    /**
     * GET all barcode with product_id, qty
     * @param string $barcode
     * @return mixed
     */
    public function updateQtyByBarcode($barcode);
}
