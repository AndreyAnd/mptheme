<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Model_Config_Source_Theme {

	/**
	 * Retrieve an array of possible options
	 *
	 * @return array
	 */
	public function toOptionArray($includeEmpty = false, $emptyText = '-- Please Select --') {
		$options = array();

		if ($includeEmpty) {
			$options[] = array(
				'value' => '',
				'label' => Mage::helper('adminhtml')->__($emptyText),
			);
		}

		foreach($this->getOptions() as $value => $label) {
			$options[] = array(
				'value' => $value,
				'label' => Mage::helper('adminhtml')->__($label),
			);
		}

		return $options;
	}

	/**
	 * Retrieve an array of possible options
	 *
	 * @return array
	 */
	public function getOptions() {
		return array(
			'default' 		=> 'Default',
			'woothemes'		=> 'WooThemes',
			'blank' 		=> 'Blank',
			'custom' 		=> 'Custom (see manual to create custom theme)',
		);
	}
}