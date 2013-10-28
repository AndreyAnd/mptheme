<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Model_Layout_Generate_Observer {

	/**
	 * Add scripts depending on configuration values
	 */
	public function loadScripts($observer) {

		if (Mage::helper('wizard')->isEnabled() == 1) {
			$_head = $this->__getHeadBlock();
			if ($_head) {
				$_head->addFirst('skin_css','wizard/css/wizard.css');
				//$_head->addFirst('skin_css','wizard/css/styles.css.php');
				$_head->addEnd('js','wizard/jquery.wizard-min.js');
				
				if (Mage::helper('wizard')->isjQueryEnabled() == 1) {
				 	if (Mage::helper('wizard')->versionjQuery() == 'latest') {
						$_head->addBefore('js','http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js','wizard/jquery.wizard-min.js');
						$_head->addAfter('js','wizard/jquery.noconflict.js','http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
					} elseif (Mage::helper('wizard')->versionjQuery() == '1.9.1') {
						$_head->addBefore('js','wizard/jquery-1.9.1.min.js','wizard/jquery.wizard-min.js');
						$_head->addAfter('js','wizard/jquery.noconflict.js','wizard/jquery-1.9.1.min.js');
					} elseif (Mage::helper('wizard')->versionjQuery() == '1.8.3') {
						$_head->addBefore('js','wizard/jquery-1.8.3.min.js','wizard/jquery.wizard-min.js');
						$_head->addAfter('js','wizard/jquery.noconflict.js','wizard/jquery-1.8.3.min.js');
					} elseif (Mage::helper('wizard')->versionjQuery() == 'oldest') {
						$_head->addBefore('js','wizard/jquery-1.4.3.min.js','wizard/jquery.wizard-min.js');
						$_head->addAfter('js','v/jquery.noconflict.js','wizard/jquery-1.4.3.min.js');
					}
					 
					$_head->addBefore('js','wizard/jquery.noconflict.js','wizard/jquery.wizard-min.js');
					
					//$_head->addAfter('js','wizard/jquery.easing.js','wizard/jquery.noconflict.js');
					//$_head->addAfter('js','wizard/jquery.mousewheel.js','wizard/jquery.easing.js');
				}
			}
		}
	}

	/*
	 * Get head block
	 */
	private function __getHeadBlock() {
		return Mage::getSingleton('core/layout')->getBlock('wizard_head');
	}

}