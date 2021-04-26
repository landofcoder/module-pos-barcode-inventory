# Mage2 Module Lof BarcodeInventory

    ``landofcoder/module-pos-barcode-inventory``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)

## Main Functionalities


## Installation
\* = in production please use the `--keep-generated` option

 - Install picqer library: `composer require picqer/php-barcode-generator`
 - Install  mpdf library: `composer require mpdf/mpdf`
 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_BarcodeInventory`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

