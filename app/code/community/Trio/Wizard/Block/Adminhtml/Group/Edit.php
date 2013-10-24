<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Block_Adminhtml_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	public function __construct() {
		parent::__construct();

		$this->_objectId	= 'id';
		$this->_controller	= 'adminhtml_group';
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
		if($this->htmlEscape(Mage::registry('group_data')->getTitle())) {
			return Mage::helper('wizard')->__("Edit Group '%s'", $this->htmlEscape(Mage::registry('group_data')->getTitle()));
		} else {
			return Mage::helper('wizard')->__("Add Group");
		}
	}
}