<?php
/**
 * @category	Trio
 * @package	Wizard
 */

class Trio_Wizard_Block_Adminhtml_Wizard_Helper_ImageScope extends Varien_Data_Form_Element_Image {

    /**
     * Prepend the base image URL to the image filename
     *
     * @return null|string
     */
    protected function _getUrl() {
            if ($this->getValue() && !is_array($this->getValue())) {
                    return Mage::helper('wizard/image')->getImageUrl($this->getValue());
            }

            return null;
    }
    
     /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        if ((string)$this->getValue()) {
            $url = $this->_getUrl();

            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media') . $url;
            }

            $html = '<a href="' . $url . '"'
                . ' onclick="imagePreview(\'' . $this->getHtmlId() . '_image\'); return false;">'
                . '<img src="' . $url . '" id="' . $this->getHtmlId() . '_image" title="' . $this->getValue() . '"'
                . ' alt="' . $this->getValue() . '" height="22" width="22" class="small-image-preview v-middle" />'
                . '</a> ';
        }
        $this->setClass('input-file');
        $html .= parent::getElementHtml();
        $html .= $this->_getDeleteCheckbox();

        return $html;
    }
    
}
