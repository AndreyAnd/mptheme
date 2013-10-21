<?php
class Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("wizard_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("wizard")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("wizard")->__("Item Information"),
				"title" => Mage::helper("wizard")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("wizard/adminhtml_wizard_edit_tab_form")->toHtml(),
				));
                                /*
                                $this->addTab("form_section", array(
				"label" => Mage::helper("wizard")->__("Item Information"),
				"title" => Mage::helper("wizard")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("wizard/adminhtml_wizard_edit_tab_usecode")->toHtml(),
				));
                                 */
                                
				return parent::_beforeToHtml();
		}

}
