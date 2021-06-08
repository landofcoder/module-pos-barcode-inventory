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
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\ProductOptionInterfaceFactory;

class FrontAddToCart implements ResolverInterface
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
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * AddToCart constructor.
     * @param GetCustomer $getCustomer
     * @param GenerateBarcodeManagementInterface $barcodeManagement
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        GetCustomer $getCustomer,
        GenerateBarcodeManagementInterface $barcodeManagement,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->getCustomer = $getCustomer;
        $this->barcodeManagement = $barcodeManagement;
        $this->quoteRepository = $quoteRepository;
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
        if (!isset($args['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }
        if (!isset($args['barcode'])) {
            throw new GraphQlInputException(__('Required parameter "barcode" is missing'));
        }
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $quote = $this->quoteRepository->getActive($args['cart_id'], [(int)$storeId]);
        if(!$quote || !$quote->getId()){
            throw new GraphQlInputException(__('The cart is empty.'));
        }
        return $this->barcodeManagement->addProductToCartByBarcode($args['barcode'], $quote->getId(), $storeId);
    }
}
