<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Model_Mysql4_Wizard extends Mage_Core_Model_Mysql4_Abstract {

	public function _construct() {
		$this->_init('wizard/wizard', 'wizard_id');
	}

	/**
	 * Logic performed before saving the model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Trio_Wizard_Model_Mysql4_Wizard
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object) {
		if (!$object->getGroupId()) {
			$object->setGroupId(null);
		}

		return parent::_beforeSave($object);
	}

	/**
	 * Retrieve the group model associated with the wizard
	 *
	 * @param Trio_Wizard_Model_Wizard $wizard
	 * @return Trio_Wizard_Model_Group
	 */
	public function getGroup(Trio_Wizard_Model_Wizard $wizard) {
		if ($wizard->getGroupId()) {
			$group = Mage::getModel('wizard/group')->load($wizard->getGroupId());

			if ($group->getId()) {
				return $group;
			}
		}

		return false;
	}

}