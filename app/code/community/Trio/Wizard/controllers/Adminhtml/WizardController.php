<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Adminhtml_WizardController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout();
		$this->_setActiveMenu('cms/wizard');
		return $this;
	}

	public function indexAction() {
		$this->_initAction();
		$this->renderLayout();
	}

	/**
	 * Display the wizard grid
	 */
	public function gridAction() {
		$this->getResponse()
			->setBody($this->getLayout()->createBlock('wizard/adminhtml_wizard_grid')->toHtml());
	}

	/**
	 * Forward to the edit action so the user can add a new wizard
	 */
	public function newAction() {
		$this->_forward('edit');
	}

	/**
	 * Display the edit/add form
	 */
	public function editAction() {
		$wizard = $this->_initWizardModel();
		$this->loadLayout();
		
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			$titles = array('Wizard');
			
			if ($wizard) {
				array_unshift($titles, 'Edit '. $wizard->getTitle());
			}
			else {
				array_unshift($titles, 'Create a Wizard');
			}

			$headBlock->setTitle(implode(' - ', $titles));
		}

		$this->renderLayout();
	}
	
	/**
	 * Save the Wizard
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('wizard')) {
			$wizard = Mage::getModel('wizard/wizard')
				->setData($data)
				->setId($this->getRequest()->getParam('id'));

			try {
				
				$hostedImage = $data['hosted_image_url'];
				
				if(empty($hostedImage)){
					$this->_handleImageUpload($wizard);
				}
				
				$wizard->save();
				$this->_getSession()->addSuccess($this->__('wizard was saved'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				Mage::logException($e);
			}
			
			if ($this->getRequest()->getParam('back') && $wizard->getId()) {
				$this->_redirect('*/*/edit', array('id' => $wizard->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save'));
		}
		
		$this->_redirect('*/*');
	}

	/**
	 * Upload an image and assign it to the model
	 *
	 * @param Trio_Wizard_Model_Wizard $wizard
	 * @param string $field = 'image'
	 */
	protected function _handleImageUpload(Trio_Wizard_Model_Wizard $wizard, $field = 'image') {
		$data = $wizard->getData($field);

		if (isset($data['value'])) {
			$wizard->setData($field, $data['value']);
		}

		if (isset($data['delete']) && $data['delete'] == '1') {
			$wizard->setData($field, '');
		}

		if ($filename = Mage::helper('wizard/image')->uploadImage($field)) {
			$wizard->setData($field, $filename);
		}
	}
	
	/**
	 * Delete a Wizard wizard
	 */
	public function deleteAction() {
		if ($wizardId = $this->getRequest()->getParam('id')) {
			$wizard = Mage::getModel('wizard/wizard')->load($wizardId);
			
			if ($wizard->getId()) {
				try {
					$wizard->delete();
					$this->_getSession()->addSuccess($this->__('The wizard was deleted.'));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}

		$this->_redirect('*/*');
	}
	
	/**
	 * Batch delete multiple Wizard wizards
	 *
	 */
	public function massDeleteAction() {
		$wizardIds = $this->getRequest()->getParam('wizard');

		if (!is_array($wizardIds)) {
			$this->_getSession()->addError($this->__('Please select some wizards.'));
		}
		else {
			if (!empty($wizardIds)) {
				try {
					foreach ($wizardIds as $wizardId) {
						$wizard = Mage::getSingleton('wizard/wizard')->load($wizardId);
	
						Mage::dispatchEvent('wizard_controller_wizard_delete', array('wizard_wizard' => $wizard));
	
						$wizard->delete();
					}
					
					$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($wizardIds)));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}

		$this->_redirect('*/*');
	}
	
	/**
	 * Batch edit multiple Wizard wizards
	 *
	 */
	public function massStatusAction() {
		$wizardIds = $this->getRequest()->getParam('wizard');
		$data = array('is_active'=>1);

		if (!is_array($wizardIds)) {
			$this->_getSession()->addError($this->__('Please select some wizards.'));
		}
		else {
			if (!empty($wizardIds)) {
				try {
					foreach ($wizardIds as $wizardId) {
						$wizard = Mage::getSingleton('wizard/wizard')
							->load($wizardId)
							->setIsEnabled($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
					}
				
				$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been edited.', count($wizardIds)));
					
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}

		$this->_redirect('*/*');
	}
	
	/**
	 * Initialise the wizard model
	 *
	 * @return null|Trio_Wizard_Model_Wizard
	 */
	protected function _initWizardModel() {
		if ($wizardId = $this->getRequest()->getParam('id')) {
			$wizard = Mage::getModel('wizard/wizard')->load($wizardId);
			
			if ($wizard->getId()) {
				Mage::register('wizard_wizard', $wizard);
			}
		}

		return Mage::registry('wizard_wizard');
	}

}