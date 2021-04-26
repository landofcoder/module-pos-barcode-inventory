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

namespace Lof\BarcodeInventory\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $data = [
            'group' => 'General',
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Barcode',
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'frontend' => '',
            'visible' => 1,
            'required' => 0,
            'is_user_defined' => 1,
            'used_for_price_rules' => 1,
            'position' => 2,
            'unique' => 0,
            'default' => '',
            'sort_order' => 100,
            'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'is_required' => 0,
            'is_configurable' => 1,
            'is_searchable' => 0,
            'is_visible_in_advanced_search' => 0,
            'is_comparable' => 0,
            'is_filterable' => 0,
            'is_filterable_in_search' => 1,
            'is_used_for_promo_rules' => 1,
            'is_html_allowed_on_front' => 0,
            'is_visible_on_front' => 1,
            'used_in_product_listing' => 1,
            'used_for_sort_by' => 0,
        ];

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'barcode',
            $data
        );
    }
}
