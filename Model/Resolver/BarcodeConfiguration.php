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

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lof\BarcodeInventory\Helper\Data as BarcodeHelper;
use Magento\GraphQl\Model\Query\ContextInterface;

class BarcodeConfiguration implements ResolverInterface
{
    /**
     * @var BarcodeHelper
     */
    private $barcodeHelper;

    /**
     * @param BarcodeHelper $barcodeHelper
     */
    public function __construct(BarcodeHelper $barcodeHelper)
    {
        $this->barcodeHelper = $barcodeHelper;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */
        if (!$context->getUserId()) {
            throw new GraphQlAuthorizationException(__('The current user isn\'t authorized.'));
        }
        $store = $context->getExtensionAttributes()->getStore();
        $storeId = $store->getId();
        return [
            'enabled' => $this->barcodeHelper->getGeneralConfig('enable', $storeId),
            'attribute_barcode' => $this->barcodeHelper->getGeneralConfig('attribute_barcode', $storeId),
            'barcode_label_content' => $this->barcodeHelper->getDesignConfig('barcode_label_content', $storeId),
            'barcode_label_css' => $this->barcodeHelper->getDesignConfig('barcode_label_css', $storeId),
            'use_label' => $this->barcodeHelper->getDesignConfig('use_label', $storeId),
            'select_label' => $this->barcodeHelper->getDesignConfig('select_label', $storeId),
            'display_logo' => $this->barcodeHelper->getDesignConfig('display_logo', $storeId),
            'logo' => $this->barcodeHelper->getDesignConfig('logo', $storeId),
            'logo_height' => $this->barcodeHelper->getDesignConfig('logo_height', $storeId),
            'logo_width' => $this->barcodeHelper->getDesignConfig('logo_width', $storeId),
        ];
    }
}
