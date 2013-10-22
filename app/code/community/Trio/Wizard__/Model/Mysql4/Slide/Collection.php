<?php
/**
 * @category	Trio
 * @package		Flexslider
 */

class Trio_Flexslider_Model_Mysql4_Slide_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	protected function _construct() {
		$this->_init('wizard/slide');
	}

	/**
	 * Init collection select
	 *
	 * @return Trio_Flexslider_Model_Mysql4_Slide_Collection
	*/
	protected function _initSelect() {
		$this->getSelect()->from(array('main_table' => $this->getMainTable()));
		
		return $this;
	}

	/**
	 * Filter the collection by a group ID
	 *
	 * @param int $groupId
	 * @return Trio_Flexslider_Model_Mysql4_Slide_Collection
	 */
	public function addGroupIdFilter($groupId) {
		return $this->addFieldToFilter('group_id', $groupId);
	}

	/**
	 * Filter the collection by enabled slides
	 *
	 * @param int $isEnabled = true
	 * @return Trio_Flexslider_Model_Mysql4_Slide_Collection
	 */
	public function addIsEnabledFilter($isEnabled = true) {
		return $this->addFieldToFilter('is_enabled', $isEnabled ? 1 : 0);
	}

	/**
	 * Add a random order to the slides
	 *
	 * @return Trio_Flexslider_Model_Mysql4_Slide_Collection
	*/
	public function addOrderByRandom($dir = 'ASC') {
		$this->getSelect()->order('RAND() ' . $dir);
		return $this;
	}

	/**
	 * Add order by sort order
	 *
	 * @return Trio_Flexslider_Model_Mysql4_Slide_Collection
	*/
	public function addOrderBySortOrder($dir = 'ASC') {
		$this->getSelect()->order('sort_order ' . $dir);
		return $this;
	}

}