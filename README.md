# Magento 2 Module Lof BarcodeInventory

    ``landofcoder/module-barcodeinventory``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)

Compatible with magento 2.3.6, 2.4.0, 2.4.1, 2.4.2, 2.4.2-p1, 2.4.3

## Main Functionalities
- Generate barcode by inventory, mutil source stock
- Add product to cart with barcode (GraphQl, REST API)
- query find products by barcode (GraphQl, REST API)

## Installation
\* = in production please use the `--keep-generated` option

### Option 1 setup via composer (Recommended):

```
composer require landofcoder/module-barcodeinventory
```

### Option 2 setup via upload FTP:
 - Unzip the zip file in `app/code/Lof`
 - Install picqer library: `composer require picqer/php-barcode-generator`
 - Install  mpdf library: `composer require mpdf/mpdf`

### Final step:
 - Enable the module by running `php bin/magento module:enable Lof_BarcodeInventory`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Example Graphql

1. Add Product To Cart by Barcode:

```
mutation{
  frontAddProductToCartByBarcode(
    cart_id: 22,
    barcode: "123456789"
  ){
    code
    message
  }
}
```

2. Get Product info by Barcode:

```

query{
  frontProductByBarcode(barcode: "123456789"){
    id
    name
    sku
		thumbnail{
      url
      label
      position
    }
    price_range{
      minimum_price{
        regular_price{
          value
          currency
        }
        final_price{
          value
          currency
        }
      }
      maximum_price{
        regular_price{
          value
          currency
        }
        final_price{
          value
          currency
        }
      }
    }
  }
}
```