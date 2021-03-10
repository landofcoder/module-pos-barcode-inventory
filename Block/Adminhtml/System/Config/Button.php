<?php
namespace Lof\BarcodeInventory\Block\Adminhtml\System\Config;

use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Button
 * @package Lof\BarcodeInventory\Block\Adminhtml\System\Config
 */
class Button extends Field
{
    /**
     * @var UrlInterface
     */
    private $_backendUrl;

    /**
     * @param UrlInterface $backendUrl
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        UrlInterface $backendUrl,
        Context $context,
        array $data = []
    ) {
        $this->_backendUrl = $backendUrl;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = "";
        $url = $this->_backendUrl->getUrl("lof_barcode/system_config/printpaper", []);
        $html .= '
        <div class="pp-buttons-container"><button type="button" id="print"><span><span><span>'.__("Print").'</span></span></span></button></div>
        <style>
        #btn_id{
            width:558px;
            background-color: #eb5202;
            color: #fff;
        }
        </style>';
        $html .= "
        <script>
        require([
                'jquery'
            ],
            function($) {
                $('#print').on('click', function(){

                    window.open('$url.');
                });
        });</script>";
        return $html;
    }
}
