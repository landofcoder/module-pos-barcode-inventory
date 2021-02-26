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


/**
 * Customer group attribute source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class PaperTemplate implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        $option = ['1' =>  __("Template 1"),
                    '2' =>  __("Template 2"),
                    '3' =>  __("Template 3"),
                    '4' =>  __("Custom")];
        $options = array();
        foreach($option as $key => $val) {
            $options[] = ['value'=>$key,'label'=>$val];
        }
        return $options;

    }
}
