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

define(
    [
        'jquery'
    ],
    function ($) {
        'use strict';
        return function (config) {
            jQuery('#file').change(function () {
                var formdata = new FormData();
                if (jQuery(this).prop('files').length > 0) {
                    file =jQuery(this).prop('files')[0];
                    formdata.append("file", file);
                    jQuery.ajax({
                        url: config.uploadInventoryUrl,
                        type: "POST",
                        data: formdata,
                        showLoader: true,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                        }
                     });
                }
            });
        };
    }
);
