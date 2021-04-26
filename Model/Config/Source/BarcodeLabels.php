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

namespace Lof\BarcodeInventory\Model\Config\Source;

use Magento\Framework\App\Action\Context;

class BarcodeLabels implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $_moduleManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $_objectManager;

    /**
     * BarcodeLabels constructor.
     * @param Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->context = $context;
        $this->_moduleManager = $moduleManager;
        $this->_objectManager = $objectmanager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->_moduleManager->isEnabled('Lof_BarcodeLabel')) {
            $list = $this->_objectManager->create("Lof\BarcodeLabel\Model\ResourceModel\Label\Collection")->addFieldToFilter(
                'status',
                'active'
            );
            foreach ($list as $item) {
                $options[] = [
                    'label' => __($item->getTemplateName()),
                    'value' => $item->getLabelId(),
                ];
            }
        } else {
            $options[] = [
                'label' => __("Module Barcode Label is not installed"),
                'value' => "0"
            ];
        }
        return $options;
    }
}
