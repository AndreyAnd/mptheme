<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Model_Config_Source_Jquery {

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
			'latest' 	=> 'Latest (hosted on Google CDN)',
			'1.9.1' 	=> 'Version 1.9.1 (hosted on local)',
			'1.8.3' 	=> 'Version 1.8.3 (hosted on local)',
			'oldest' 	=> 'Version 1.4.3 (not recommended)',
		);
	}

}