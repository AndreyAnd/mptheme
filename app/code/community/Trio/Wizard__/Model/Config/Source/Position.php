<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Model_Config_Source_Position {
	const CONTENT_TOP		= 'CONTENT_TOP';
	const CONTENT_BOTTOM	= 'CONTENT_BOTTOM';

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return array(
			array('value' => self::CONTENT_TOP, 'label'=>Mage::helper('adminhtml')->__('Content Top')),
			array('value' => self::CONTENT_BOTTOM, 'label'=>Mage::helper('adminhtml')->__('Content Bottom'))
		);
	}

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toGridOptionArray()
	{
		return array(
			self::CONTENT_TOP => Mage::helper('adminhtml')->__('Content Top'),
			self::CONTENT_BOTTOM => Mage::helper('adminhtml')->__('Content Bottom')
		);
	}
}