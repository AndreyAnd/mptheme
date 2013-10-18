<?php
	
class Trio_Wizard_Block_Adminhtml_Wizard_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "wizard_id";
				$this->_blockGroup = "wizard";
				$this->_controller = "adminhtml_wizard";
				$this->_updateButton("save", "label", Mage::helper("wizard")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("wizard")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("wizard")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("wizard_data") && Mage::registry("wizard_data")->getId() ){

				    return Mage::helper("wizard")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("wizard_data")->getId()));

				} 
				else{

				     return Mage::helper("wizard")->__("Add Item");

				}
		}
}