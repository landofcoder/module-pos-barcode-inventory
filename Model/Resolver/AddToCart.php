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

declare(strict_types=1);

namespace Lof\BarcodeInventory\Model\Resolver;

use Lof\BarcodeInventory\Api\GenerateBarcodeManagementInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;

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
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return \Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws GraphQlAuthorizationException
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
        if (!isset($args['cartId'])) {
            $args['cartId'] = 0;
        }
        return $this->barcodeManagement->addProductToCartByBarcode($args['barcode'], $args['cartId']);
    }
}
