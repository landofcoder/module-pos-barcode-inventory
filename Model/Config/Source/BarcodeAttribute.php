<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_QtyDropdown
 * @copyright  Copyright (c) 2020 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\BarcodeInventory\Model\Config\Source;

class BarcodeAttribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = ['sku' =>  __("Product SKU"),
                    'product_code' =>  __("Product Code")];
        $options = [];
        foreach ($option as $key => $val) {
            $options[] = ['value'=>$key,'label'=>$val];
        }
        return $options;
    }
}
