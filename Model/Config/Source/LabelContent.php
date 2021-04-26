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

namespace Lof\BarcodeInventory\Model\Config\Source;

class LabelContent implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = ['1' =>  "<div class=\"barcode_paper\">
						<div class=\"row\"><b>{{product_name}}</b></div>
						<div class=\"row\">{{barcode}}</div>
						<div class=\"row\"><b>{{product_sku}}</b></div>
						<div class=\"row\"><b>{{product_price}}</b></div>
						<div class=\"row\"><b>{{barcode_number}}</b></div>
					</div>"];
        $options = [];
        foreach ($option as $key => $val) {
            $options[] = ['value'=>$key,'label'=>$val];
        }
        return $options;
    }
}
