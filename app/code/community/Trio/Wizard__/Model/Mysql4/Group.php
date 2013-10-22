<?php
/**
 * @category	Trio
 * @package		Flexslider
 */

class Trio_Flexslider_Model_Mysql4_Group extends Mage_Core_Model_Mysql4_Abstract {

	protected function _construct() {
		$this->_init('wizard/group', 'group_id');
	}

	/**
	 *
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
		if (!$object->getIsMassDelete()) {
			$object = $this->__loadStore($object);
			$object = $this->__loadPage($object);
			$object = $this->__loadCategory($object);
		}

		return parent::_afterLoad($object);
	}

	/**
	 * Retrieve select object for load object data
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return Zend_Db_Select
	 */
	protected function _getLoadSelect($field, $value, $object) {
		$select = parent::_getLoadSelect($field, $value, $object);

		if ($data = $object->getStoreId()) {
			$select->join(
					array('store' => $this->getTable('wizard/wizard_store')), $this->getMainTable().'.group_id = `store`.group_id')
					->where('`store`.store_id in (0, ?) ', $data);
		}
		if ($data = $object->getPageId()) {
			$select->join(
					array('page' => $this->getTable('wizard/wizard_page')), $this->getMainTable().'.group_id = `page`.group_id')
					->where('`page`.page_id in (?) ', $data);
		}
		if ($data = $object->getCategoryId()) {
			$select->join(
					array('category' => $this->getTable('wizard/wizard_category')), $this->getMainTable().'.group_id = `category`.group_id')
					->where('`category`.category_id in (?) ', $data);
		}
		$select->order('title DESC')->limit(1);

		return $select;
	}

	/**
	 * Call-back function
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object) {
		if (!$object->getIsMassStatus()) {
			$this->__saveToStoreTable($object);
			$this->__saveToPageTable($object);
			$this->__saveToCategoryTable($object);
		}

		return parent::_afterSave($object);
	}

	/**
	 * Call-back function
	 */
	protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
		$adapter = $this->_getReadAdapter();
		$adapter->delete($this->getTable('wizard/wizard_store'), 'group_id='.$object->getId());
		$adapter->delete($this->getTable('wizard/wizard_page'), 'group_id='.$object->getId());
		$adapter->delete($this->getTable('wizard/wizard_category'), 'group_id='.$object->getId());

		return parent::_beforeDelete($object);
	}

	/**
	 * Load stores
	 */
	private function __loadStore(Mage_Core_Model_Abstract $object) {
		$select = $this->_getReadAdapter()->select()
				->from($this->getTable('wizard/wizard_store'))
				->where('group_id = ?', $object->getId());

		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$array = array();
			foreach ($data as $row) {
				$array[] = $row['store_id'];
			}
			$object->setData('store_id', $array);
		}
		return $object;
	}

	/**
	 * Load pages
	 */
	private function __loadPage(Mage_Core_Model_Abstract $object) {
		$select = $this->_getReadAdapter()->select()
				->from($this->getTable('wizard/wizard_page'))
				->where('group_id = ?', $object->getId());

		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$array = array();
			foreach ($data as $row) {
				$array[] = $row['page_id'];
			}
			$object->setData('page_id', $array);
		}
		return $object;
	}

	/**
	 * Load categories
	 */
	private function __loadCategory(Mage_Core_Model_Abstract $object) {
		$select = $this->_getReadAdapter()->select()
				->from($this->getTable('wizard/wizard_category'))
				->where('group_id = ?', $object->getId());

		if ($data = $this->_getReadAdapter()->fetchAll($select)) {
			$array = array();
			foreach ($data as $row) {
				$array[] = $row['category_id'];
			}
			$object->setData('category_id', $array);
		}
		return $object;
	}

	/**
	 * Save stores
	 */
	private function __saveToStoreTable(Mage_Core_Model_Abstract $object) {
		if (!$object->getData('stores')) {
			$condition = $this->_getWriteAdapter()->quoteInto('group_id = ?', $object->getId());
			$this->_getWriteAdapter()->delete($this->getTable('wizard/wizard_store'), $condition);

			$storeArray = array(
				'group_id' => $object->getId(),
				'store_id' => '0');
			$this->_getWriteAdapter()->insert(
					$this->getTable('wizard/wizard_store'), $storeArray);
			return true;
		}

		$condition = $this->_getWriteAdapter()->quoteInto('group_id = ?', $object->getId());
		$this->_getWriteAdapter()->delete($this->getTable('wizard/wizard_store'), $condition);
		foreach ((array)$object->getData('stores') as $store) {
			$storeArray = array();
			$storeArray['group_id'] = $object->getId();
			$storeArray['store_id'] = $store;
			$this->_getWriteAdapter()->insert(
					$this->getTable('wizard/wizard_store'), $storeArray);
		}
	}

	/**
	 * Save pages
	 */
	private function __saveToPageTable(Mage_Core_Model_Abstract $object) {
		if ($data = $object->getData('pages')) {

			$this->_getWriteAdapter()->beginTransaction();
			try {
				$condition = $this->_getWriteAdapter()->quoteInto('group_id = ?', $object->getId());
				$this->_getWriteAdapter()->delete($this->getTable('wizard/wizard_page'), $condition);

				foreach ((array)$data as $page) {
					$pageArray = array();
					$pageArray['group_id'] = $object->getId();
					$pageArray['page_id'] = $page;
					$this->_getWriteAdapter()->insert(
							$this->getTable('wizard/wizard_page'), $pageArray);
				}
				$this->_getWriteAdapter()->commit();
			} catch (Exception $e) {
				$this->_getWriteAdapter()->rollBack();
				echo $e->getMessage();
			}
			return true;
		}

		$condition = $this->_getWriteAdapter()->quoteInto('group_id = ?', $object->getId());
		$this->_getWriteAdapter()->delete($this->getTable('wizard/wizard_page'), $condition);
	}

	/**
	 * Save categories
	 */
	private function __saveToCategoryTable(Mage_Core_Model_Abstract $object) {
		if ($data = $object->getData('categories')) {

			$this->_getWriteAdapter()->beginTransaction();
			try {
				$condition = $this->_getWriteAdapter()->quoteInto('group_id = ?', $object->getId());
				$this->_getWriteAdapter()->delete($this->getTable('wizard/wizard_category'), $condition);

				$data = array_unique($data);
				foreach ((array)$data as $category) {
					$categoryArray = array();
					$categoryArray['group_id'] = $object->getId();
					$categoryArray['category_id'] = $category;
					$this->_getWriteAdapter()->insert(
							$this->getTable('wizard/wizard_category'), $categoryArray);
				}
				$this->_getWriteAdapter()->commit();
			} catch (Exception $e) {
				$this->_getWriteAdapter()->rollBack();
				echo $e->getMessage();
			}
			return true;
		}

		$condition = $this->_getWriteAdapter()->quoteInto('group_id = ?', $object->getId());
		$this->_getWriteAdapter()->delete($this->getTable('wizard/wizard_category'), $condition);
	}

}