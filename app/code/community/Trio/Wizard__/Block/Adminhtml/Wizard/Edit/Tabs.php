<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Block_Adminhtml_Slide_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

	public function __construct() {
		parent::__construct();
		$this->setId('wizard_slide_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('wizard')->__('Wizard - Slides'));
	}

	protected function _beforeToHtml() {
		$this->addTab('general',
			array(
				'label' => $this->__('General'),
				'title' => $this->__('General'),
				'content' => $this->getLayout()->createBlock('wizard/adminhtml_slide_edit_tab_form')->toHtml(),
			)
		);

		return parent::_beforeToHtml();
	}
}