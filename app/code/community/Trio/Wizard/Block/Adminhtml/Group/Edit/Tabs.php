<?php
/**
 * @category	Trio
 * @package		Wizard
 */
 
class Trio_Wizard_Block_Adminhtml_Group_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

	public function __construct() {
		parent::__construct();
		$this->setId('wizard_group_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('wizard')->__('Wizard - Groups'));
	}

	protected function _beforeToHtml() {
		
                $this->addTab('general_section', array(
			'label'		=> Mage::helper('wizard')->__('General Information'),
			'title'		=> Mage::helper('wizard')->__('General Information'),
			'content'	=> $this->getLayout()->createBlock('wizard/adminhtml_group_edit_tab_form')->toHtml(),
		));
                
                $this->addTab('page_section', array(
			'label'		=> Mage::helper('wizard')->__('Display on Pages'),
			'title'		=> Mage::helper('wizard')->__('Display on Pages'),
			'content'	=> $this->getLayout()->createBlock('wizard/adminhtml_group_edit_tab_page')->toHtml(),
		));
                
                $this->addTab('category_section', array(
			'label'		=> Mage::helper('wizard')->__('Display on Categories'),
			'title'		=> Mage::helper('wizard')->__('Display on Categories'),
			'content'	=> $this->getLayout()->createBlock('wizard/adminhtml_group_edit_tab_category')->toHtml(),
		));
		
                if ($this->getRequest()->getParam('id')) {
			$this->addTab('wizards_section', array(
				'label'		=> Mage::helper('wizard')->__('Wizards of this Group'),
				'title'		=> Mage::helper('wizard')->__('Wizards of this Group'),
				'content'	=> $this->getLayout()->createBlock('wizard/adminhtml_group_edit_tab_wizards')->toHtml(),
			));
                       /*
                       $this->addTab('xml_section', array(
				'label'		=> Mage::helper('wizard')->__('Use Code Inserts'),
				'title'		=> Mage::helper('wizard')->__('Use Code Inserts'),
				'content'	=> $this->getLayout()->createBlock('wizard/adminhtml_group_edit_tab_XML')->toHtml(),
			));*/
		}
		

		return parent::_beforeToHtml();
	}
}