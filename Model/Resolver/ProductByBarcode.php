<?php
/**
 * Copyright © LandOfCoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\BarcodeInventory\Model\Resolver;

use Lof\BarcodeInventory\Api\GenerateBarcodeManagementInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;


/**
 * Class ProductByBarcode
 * @package Lof\BarcodeInventory\Model\Resolver
 */
class ProductByBarcode implements ResolverInterface
{

    /**
     * @var GenerateBarcodeManagementInterface
     */
    private $barcodeManagement;

    /**
     * ProductByBarcode constructor.
     * @param GenerateBarcodeManagementInterface $generateBarcodeManagement
     */
    public function __construct(
        GenerateBarcodeManagementInterface $generateBarcodeManagement
    ) {
        $this->barcodeManagement = $generateBarcodeManagement;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        return $this->barcodeManagement->getProductInfo($args['barcode']);
    }
}

