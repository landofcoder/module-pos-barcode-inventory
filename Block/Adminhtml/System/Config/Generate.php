<?php
namespace Lof\BarcodeInventory\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\UrlInterface;

class Generate extends \Magento\Config\Block\System\Config\Form\Field
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = "";
        $url = $this->_backendUrl->getUrl("lof_barcode/system_config/generatebarcode", []);
        $html .= '
        <div class="pp-buttons-container"><button type="button" id="generate"><span><span><span>'.__("RUN").'</span></span></span></button></div>
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
                'jquery',
                'Magento_Ui/js/modal/confirm'
            ],
            function($, confirmation) {
                $('#generate').on('click', function(event){
                    event.preventDefault;
                    confirmation({
                        title: '".__("You are really want to generate all barcode?")."',
                        content: '',
                        actions: {
                            confirm: function () {
                            jQuery.ajax({
                                    url : '$url',
                                    showLoader: true,
                                    type : 'POST',
                                    data: {
                                        format: 'json'
                                    },
                                    dataType:'json',
                                    success : function() {
                                        alert('All Barcodes are Generated');
                                    },
                                    error : function(request,error)
                                    {
                                        alert(\"Error\");
                                    }
                                });
                            },
                        }
                    });
                });
            });</script>";
        return $html;
    }
}
