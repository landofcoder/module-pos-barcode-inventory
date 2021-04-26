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

namespace Lof\BarcodeInventory\Model;

class CsvFile
{
    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * CsvFile constructor.
     * @param \Magento\Framework\File\Csv $csvProcessor
     */
    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * @param $file
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $importProductRawData = $this->csvProcessor->getData($file['tmp_name']);

        foreach ($importProductRawData as $dataRow) {
            \Zend_Debug::dump($dataRow);
        }

        die();
    }
}
