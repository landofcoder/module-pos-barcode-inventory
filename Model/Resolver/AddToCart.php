<?php
declare(strict_types=1);

namespace Lof\BarcodeInventory\Model\Resolver;

use Lof\BarcodeInventory\Api\GenerateBarcodeManagementInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;


/**
 * Class AddToCart
 * @package Lof\BarcodeInventory\Model\Resolver
 */
class AddToCart implements ResolverInterface
{


    /**
     * @var GetCustomer
     */
    private $getCustomer;
    /**
     * @var GenerateBarcodeManagementInterface
     */
    private $barcodeManagement;

    /**
     * AddToCart constructor.
     * @param GetCustomer $getCustomer
     * @param GenerateBarcodeManagementInterface $barcodeManagement
     */
    public function __construct(
        GetCustomer $getCustomer,
        GenerateBarcodeManagementInterface $barcodeManagement
    ) {
        $this->getCustomer = $getCustomer;
        $this->barcodeManagement = $barcodeManagement;
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
        if (!isset($args['cartId'])){
            $args['cartId'] = 0;
        }
        return $this->barcodeManagement->addProductToCartByBarcode($args['barcode'], $args['cartId']);
    }
}