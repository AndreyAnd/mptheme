<?php

class Trio_Wizard_Adminhtml_WizardController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("wizard/wizard")->_addBreadcrumb(Mage::helper("adminhtml")->__("Wizard  Manager"),Mage::helper("adminhtml")->__("Wizard Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Wizard"));
			    $this->_title($this->__("Manager Wizard"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Wizard"));
				$this->_title($this->__("Wizard"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("wizard/wizard")->load($id);
				if ($model->getId()) {
					Mage::register("wizard_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("wizard/wizard");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Wizard Manager"), Mage::helper("adminhtml")->__("Wizard Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Wizard Description"), Mage::helper("adminhtml")->__("Wizard Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("wizard/adminhtml_wizard_edit"))->_addLeft($this->getLayout()->createBlock("wizard/adminhtml_wizard_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("wizard")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Wizard"));
		$this->_title($this->__("Wizard"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("wizard/wizard")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("wizard_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("wizard/wizard");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Wizard Manager"), Mage::helper("adminhtml")->__("Wizard Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Wizard Description"), Mage::helper("adminhtml")->__("Wizard Description"));


		$this->_addContent($this->getLayout()->createBlock("wizard/adminhtml_wizard_edit"))->_addLeft($this->getLayout()->createBlock("wizard/adminhtml_wizard_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						
				 //save image
		try{

if((bool)$post_data['image']['delete']==1) {

	        $post_data['image']='';

}
else {

	unset($post_data['image']);

	if (isset($_FILES)){

		if ($_FILES['image']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("wizard/wizard")->load($this->getRequest()->getParam("id"));
				if($model->getData('image')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('image'))));	
				}
			}
						$path = Mage::getBaseDir('media') . DS . 'wizard' . DS .'wizard'.DS;
						$uploader = new Varien_File_Uploader('image');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['image']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['image']='wizard/wizard/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image


						$model = Mage::getModel("wizard/wizard")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Wizard was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setWizardData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setWizardData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("wizard/wizard");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('wizard_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("wizard/wizard");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
}
