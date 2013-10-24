<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Adminhtml_SlideController extends Mage_Adminhtml_Controller_Action {

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
	 * Display the slide grid
	 */
	public function gridAction() {
		$this->getResponse()
			->setBody($this->getLayout()->createBlock('wizard/adminhtml_slide_grid')->toHtml());
	}

	/**
	 * Forward to the edit action so the user can add a new slide
	 */
	public function newAction() {
		$this->_forward('edit');
	}

	/**
	 * Display the edit/add form
	 */
	public function editAction() {
		$slide = $this->_initSlideModel();
		$this->loadLayout();
		
		if ($headBlock = $this->getLayout()->getBlock('head')) {
			$titles = array('Wizard');
			
			if ($slide) {
				array_unshift($titles, 'Edit '. $slide->getTitle());
			}
			else {
				array_unshift($titles, 'Create a Slide');
			}

			$headBlock->setTitle(implode(' - ', $titles));
		}

		$this->renderLayout();
	}
	
	/**
	 * Save the slide
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('slide')) {
			$slide = Mage::getModel('wizard/slide')
				->setData($data)
				->setId($this->getRequest()->getParam('id'));

			try {
				
				$hostedImage = $data['hosted_image_url'];
				
				if(empty($hostedImage)){
					$this->_handleImageUpload($slide);
				}
				
				$slide->save();
				$this->_getSession()->addSuccess($this->__('Slide was saved'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				Mage::logException($e);
			}
			
			if ($this->getRequest()->getParam('back') && $slide->getId()) {
				$this->_redirect('*/*/edit', array('id' => $slide->getId()));
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
	 * @param Trio_Wizard_Model_Slide $slide
	 * @param string $field = 'image'
	 */
	protected function _handleImageUpload(Trio_Wizard_Model_Slide $slide, $field = 'image') {
		$data = $slide->getData($field);

		if (isset($data['value'])) {
			$slide->setData($field, $data['value']);
		}

		if (isset($data['delete']) && $data['delete'] == '1') {
			$slide->setData($field, '');
		}

		if ($filename = Mage::helper('wizard/image')->uploadImage($field)) {
			$slide->setData($field, $filename);
		}
	}
	
	/**
	 * Delete a Wizard slide
	 */
	public function deleteAction() {
		if ($slideId = $this->getRequest()->getParam('id')) {
			$slide = Mage::getModel('wizard/slide')->load($slideId);
			
			if ($slide->getId()) {
				try {
					$slide->delete();
					$this->_getSession()->addSuccess($this->__('The slide was deleted.'));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}

		$this->_redirect('*/*');
	}
	
	/**
	 * Batch delete multiple Wizard slides
	 *
	 */
	public function massDeleteAction() {
		$slideIds = $this->getRequest()->getParam('slide');

		if (!is_array($slideIds)) {
			$this->_getSession()->addError($this->__('Please select some slides.'));
		}
		else {
			if (!empty($slideIds)) {
				try {
					foreach ($slideIds as $slideId) {
						$slide = Mage::getSingleton('wizard/slide')->load($slideId);
	
						Mage::dispatchEvent('wizard_controller_slide_delete', array('wizard_slide' => $slide));
	
						$slide->delete();
					}
					
					$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($slideIds)));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}

		$this->_redirect('*/*');
	}
	
	/**
	 * Batch edit multiple Wizard slides
	 *
	 */
	public function massStatusAction() {
		$slideIds = $this->getRequest()->getParam('slide');
		$data = array('is_active'=>1);

		if (!is_array($slideIds)) {
			$this->_getSession()->addError($this->__('Please select some slides.'));
		}
		else {
			if (!empty($slideIds)) {
				try {
					foreach ($slideIds as $slideId) {
						$slide = Mage::getSingleton('wizard/slide')
							->load($slideId)
							->setIsEnabled($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
					}
				
				$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been edited.', count($slideIds)));
					
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}

		$this->_redirect('*/*');
	}
	
	/**
	 * Initialise the slide model
	 *
	 * @return null|Trio_Wizard_Model_Slide
	 */
	protected function _initSlideModel() {
		if ($slideId = $this->getRequest()->getParam('id')) {
			$slide = Mage::getModel('wizard/slide')->load($slideId);
			
			if ($slide->getId()) {
				Mage::register('wizard_slide', $slide);
			}
		}

		return Mage::registry('wizard_slide');
	}

}