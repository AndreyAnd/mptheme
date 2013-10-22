<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Block_Adminhtml_Group_Edit_Tab_Page extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
	
		$_model = Mage::registry('group_data');
		$form = new Varien_Data_Form();
		$this->setForm($form);

		$fieldset = $form->addFieldset('page_form', array('legend'=>Mage::helper('wizard')->__('Group Pages')));

		$fieldset->addField('pages', 'multiselect', array(
			'name'		=> 'pages[]',
			'label'		=> Mage::helper('wizard')->__('Visible In'),
			'required'	=> false,
			'values'	=> Mage::getSingleton('wizard/config_source_page')->toOptionArray(),
			'value'		=> $_model->getPageId()
		));

		return parent::_prepareForm();
	}
}
