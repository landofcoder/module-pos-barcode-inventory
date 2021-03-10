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
 * Class Barcodes
 * @package Lof\BarcodeInventory\Model\Resolver
 */
class Barcodes implements ResolverInterface
{

    /**
     * @var GenerateBarcodeManagementInterface
     */
    private $barcodeManagement;

    /**
     * Barcodes constructor.
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
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
        return $this->barcodeManagement->getAllBarcode($args['pageSize'], $args['currentPage']);
    }
}

