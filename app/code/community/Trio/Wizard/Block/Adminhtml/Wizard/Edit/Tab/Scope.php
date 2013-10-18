<?php
class Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scope
extends Mage_Adminhtml_Block_Widget
implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Initialize block
     */
    public function __construct()
    {
        Mage::log(' ----------  Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scope  55555555 /n');
		//$this->setTemplate('wizard/scope/allow.phtml');// app/design/adminhtml/default/default/template/wizard/scope/allow.phtml
    }
 
	/**
	* renderer
	*
	* @param Varien_Data_Form_Element_Abstract $element
	*/
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		Mage::log('Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scope  33333333 /n');
		
		$element->setDisabled(true);
		$disabled = true;
		$htmlId = 'use_config_' . $element->getHtmlId();
		$html = '' . $element->getLabelHtml() . '';
		//$html .= $element->getId() . '"'. ($disabled ? ' checked="checked"' : '');
		//$html .= ' onclick="toggleValueElements(this, this.parentNode);" class="checkbox" type="checkbox" />';
		$html .= ' <label for="' . $htmlId . '">' . 'Do not change value' . '</label>';
		$html .= $element->getElementHtml();
		//$html .= 'toggleValueElements($(\'' . $htmlId . '\'), $(\'' . $htmlId . '\').parentNode);';
		 
		 $html.='55555555555555555555555';
		return $html;
	}
	/**
     * Form element instance
     *
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element;

    /**
     * Customer groups cache
     *
     * @var array
     */
    protected $_customerGroups;

    /**
     * Websites cache
     *
     * @var array
     */
    protected $_websites;

    /**
     * Retrieve current product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }
	/**
     * Set form element instance
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group_Abstract
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve form element instance
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Prepare group price values
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $data = $this->getElement()->getValue();
        
        if (is_array($data)) {
            $values = $this->_sortValues($data);
        }

        return $values;
    }

	 /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        return $data;
    }

}