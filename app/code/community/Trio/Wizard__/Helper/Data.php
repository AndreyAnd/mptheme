<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 * Encode the mixed $valueToEncode into the JSON format
	 *
	 * @param mixed $valueToEncode
	 * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
	 * @param  array $options Additional options used during encoding
	 * @return string
	 */
	public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array()) {
		$json = Zend_Json::encode($valueToEncode, $cycleCheck, $options);
		/* @var $inline Mage_Core_Model_Translate_Inline */
		$inline = Mage::getSingleton('core/translate_inline');
		if ($inline->isAllowed()) {
			$inline->setIsJson(true);
			$inline->processResponseBody($json);
			$inline->setIsJson(false);
		}

		return $json;
	}

	/**
	 * Determine whether the extension is enabled
	 *
	 * @return bool
	 */
	public function isEnabled() {
		return Mage::getStoreConfig('wizard/general/enabled');
	}

	/**
	 * Determine whether jQuery should be loaded
	 *
	 * @return bool
	 */
	public function isjQueryEnabled() {
		return Mage::getStoreConfig('wizard/general/enable_jquery');
	}

	/**
	 * Determine which jQuery version should be loaded
	 *
	 * @return bool
	 */
	public function versionjQuery() {
		return Mage::getStoreConfig('wizard/general/version_jquery');
	}

}