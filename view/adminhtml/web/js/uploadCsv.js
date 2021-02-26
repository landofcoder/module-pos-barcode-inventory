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