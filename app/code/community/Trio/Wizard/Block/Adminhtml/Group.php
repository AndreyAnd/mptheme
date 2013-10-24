<?php
/**
 * @category	Trio
 * @package		Wizard
 */
 
class Trio_Wizard_Block_Adminhtml_Group extends Mage_Adminhtml_Block_Widget_Grid_Container {
	
	public function __construct() {
		$this->_controller = 'adminhtml_group';
		$this->_blockGroup = 'wizard';
		$this->_headerText = $this->__('Groups - Wizard');
		parent::__construct();
	}

	protected function _prepareLayout() {

		/**
		 * Display store switcher if system has more one store
		 */
		if (!Mage::app()->isSingleStoreMode()) {
			$this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
					->setUseConfirm(false)
					->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
			);
		}

		return parent::_prepareLayout();
	}

	public function getStoreSwitcherHtml() {
		return $this->getChildHtml('store_switcher');
	}

}