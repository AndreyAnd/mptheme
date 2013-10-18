<?php
class Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scopetype extends Varien_Data_Form_Element_Multiselect
{
	protected function _construct()
        {
            parent::_construct();
            
        }
	/**
	* Retrieve Element HTML fragment
	*
	* @return string
	*/
	public function getElementHtml()
	{
			Mage::log(' ----------  Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scopetype  777777777 /n');
		$disabled = false;
		if (!$this->getValue()) {
		$this->setData('disabled', 'disabled');
		$disabled = true;
		}
		 
		$html = parent::getElementHtml();
		$htmlId = 'use_config_' . $this->getHtmlId();
		$html .=  $this->getId() . '"';
		$html .= ($disabled ? ' checked="checked"' : '') . ($this->getReadonly()? ' disabled="disabled"':'');
		$html .= ' onclick="toggleValueElements(this, this.parentNode);" class="checkbox" type="checkbox" />';
		$html .= ' <label for="' . $htmlId . '">' . $this->getCheckboxLabel() . '</label>';
		$html .= 'toggleValueElements($(\'' . $htmlId . '\'), $(\'' . $htmlId . '\').parentNode);';
		return $html;
	}
 
}