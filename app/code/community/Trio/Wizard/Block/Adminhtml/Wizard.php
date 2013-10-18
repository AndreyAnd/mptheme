<?php


class Trio_Wizard_Block_Adminhtml_Wizard extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_wizard";
	$this->_blockGroup = "wizard";
	$this->_headerText = Mage::helper("wizard")->__("Wizard Manager");
	$this->_addButtonLabel = Mage::helper("wizard")->__("Add New Item");
	parent::__construct();
	
	}

}