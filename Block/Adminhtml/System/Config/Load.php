<?php
namespace Lof\BarcodeInventory\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Load extends Field
{
    /**
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_backendUrl = $backendUrl;
        parent::__construct($context, $data);
    }
    /**
     * Add color picker
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = "";
        $html .= '
        <div class="pp-buttons-container"><button type="button" id="load"><span><span><span>'.__("Load").'</span></span></span></button></div>
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
                $('#load').on('click', function(){
                    alert('Barcode Generated');
                    
                });
            });</script>";
        return $html;
    }
}
