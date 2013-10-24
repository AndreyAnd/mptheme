<?php
/**
 * @category	Trio
 * @package		wizard
 */

class Trio_Wizard_Block_Adminhtml_Wizard_Edit  extends Mage_Adminhtml_Block_Widget_Form_Container {

	public function __construct() {
		parent::__construct();

		$this->_objectId	= 'id';
		$this->_controller	= 'adminhtml_wizard';
		$this->_blockGroup	= 'wizard';

		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText() {
		if(Mage::registry('wizard_wizard')) {
			return Mage::helper('wizard')->__("Edit Wizard '%s'", $this->htmlEscape(Mage::registry('wizard_wizard')->getTitle()));
		} else {
			return Mage::helper('wizard')->__("Add Wizard");
		}
	}

	protected function _prepareLayout() {
		parent::_prepareLayout();
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
	}
}