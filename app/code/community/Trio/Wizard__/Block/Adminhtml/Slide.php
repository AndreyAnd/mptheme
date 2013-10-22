<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Block_Adminhtml_Slide extends Mage_Adminhtml_Block_Widget_Grid_Container {

	public function __construct() {
		$this->_controller = 'adminhtml_slide';
		$this->_blockGroup = 'wizard';
		$this->_headerText = $this->__('Slides - Wizard');
		parent::__construct();
	}

}