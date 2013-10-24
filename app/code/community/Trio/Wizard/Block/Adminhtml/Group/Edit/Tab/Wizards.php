<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Block_Adminhtml_Group_Edit_Tab_Wizards extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId('wizard_grid');
		$this->setDefaultSort('title');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Initialise and set the collection for the grid
	 */
	protected function _prepareCollection() {
		$wizard_collection = Mage::getModel('wizard/wizard')->getCollection()
			->addGroupIdFilter($this->getGroupId());

		$this->setCollection($wizard_collection);
	
		return parent::_prepareCollection();
	}
	
	/**
	 * Add the columns to the grid
	 */
	protected function _prepareColumns() {
		$this->addColumn('wizard_id', array(
			'header'	=> $this->__('ID'),
			'align'		=> 'left',
			'width'		=> '60px',
			'index'		=> 'wizard_id',
		));

		$this->addColumn('wizard_title', array(
			'header'	=> $this->__('Title'),
			'align'		=> 'left',
			'index'		=> 'title',
		));

		$this->addColumn('is_enabled', array(
			'header'	=> $this->__('Enabled'),
			'width'		=> '90px',
			'index'		=> 'is_enabled',
			'type'		=> 'options',
			'options'	=> array(
				1 => $this->__('Enabled'),
				0 => $this->__('Disabled'),
			),
		));

		return parent::_prepareColumns();
	}

	/**
	 * Disable the edit URL for the row as this grid is for viewing only
	 */
	public function getRowUrl($row) {
		//return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	/**
	 * Retrieve the group ID
	 * @return int
	 */
	public function getGroupId() {
		return Mage::registry('current_group') ? Mage::registry('current_group')->getId() : 0;
	}
}
