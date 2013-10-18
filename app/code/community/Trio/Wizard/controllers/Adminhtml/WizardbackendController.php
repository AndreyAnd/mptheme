<?php
class Trio_Wizard_Adminhtml_WizardbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Wizard Backend Page Title"));
	   $this->renderLayout();
    }
}