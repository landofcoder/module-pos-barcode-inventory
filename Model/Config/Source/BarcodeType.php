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

class BarcodeType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = [
            'TYPE_CODE_39' => __("Code 39"),
            'TYPE_CODE_39_CHECKSUM' => __("Code 39+"),
            'TYPE_CODE_39E' => __("Code 39 E"),
            'TYPE_CODE_39E_CHECKSUM' => __("Code 39 E+"),
            'TYPE_CODE_93' => __("C93"),
            'TYPE_STANDARD_2_5' => __("Standard 2 of 5"),
            'TYPE_STANDARD_2_5_CHECKSUM' => __("Standard 2 of 5+"),
            'TYPE_INTERLEAVED_2_5' => __("Interleaved 2 of 5"),
            'TYPE_INTERLEAVED_2_5_CHECKSUM' => __("Interleaved 2 of 5+"),
            'TYPE_CODE_128' => __("Code 128"),
            'TYPE_CODE_128_A' => __("Code 128 A"),
            'TYPE_CODE_128_B' => __("Code 128 B"),
            'TYPE_CODE_128_C' => __("Code 128 C"),
            'TYPE_EAN_2' => __("EAN 2"),
            'TYPE_EAN_5' => __("EAN 5"),
            'TYPE_EAN_8' => __("EAN 8"),
            'TYPE_EAN_13' => __("EAN 13"),
            'TYPE_UPC_A' => __("UPC A"),
            'TYPE_UPC_E' => __("UPC E"),
            'TYPE_MSI' => __("MSI"),
            'TYPE_MSI_CHECKSUM' => __("MSI+"),
            'TYPE_POSTNET' => __("POSTNET"),
            'TYPE_PLANET' => __("PLANET"),
            'TYPE_RMS4CC' => __("RMS4CC"),
            'TYPE_KIX' => __("KIX"),
            'TYPE_IMB' => __("IMB"),
            'TYPE_CODABAR' => __("CODABAR"),
            'TYPE_CODE_11' => __("CODE 11"),
            'TYPE_PHARMA_CODE' => __("PHARMA CODE"),
            'TYPE_PHARMA_CODE_TWO_TRACKS' => __("PHARMA CODE 2 TRACKS")
        ];
        $options = [];
        foreach ($option as $key => $val) {
            $options[] = ['value' => $key, 'label' => $val];
        }

        return $options;
    }
}
