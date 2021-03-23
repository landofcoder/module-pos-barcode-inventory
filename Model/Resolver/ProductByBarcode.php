<?php
/**
 * Copyright © LandOfCoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\BarcodeInventory\Model\Resolver;

use Lof\BarcodeInventory\Api\GenerateBarcodeManagementInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;


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
        /** @var ContextInterface $context */
        if (!$context->getUserId()) {
            throw new GraphQlAuthorizationException(__('The current user isn\'t authorized.'));
        }
        return $this->barcodeManagement->getProductInfo($args['barcode']);
    }
}

