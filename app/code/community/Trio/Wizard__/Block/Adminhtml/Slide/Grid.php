<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Block_Adminhtml_Slide_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId('slide_grid');
		$this->setDefaultSort('slide_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Initialise and set the collection for the grid
	 */
	protected function _prepareCollection() {
		$collection = Mage::getModel('wizard/slide')->getCollection();

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Add the columns to the grid
	 */
	protected function _prepareColumns() {

		$this->addColumn('slide_id', array(
			'header'	=> Mage::helper('wizard')->__('ID'),
			'align'		=> 'left',
			'width'		=> '60px',
			'index'		=> 'slide_id',
		));

		$this->addColumn('title', array(
			'header'		=> Mage::helper('wizard')->__('Title'),
			'align'			=> 'left',
			'index'			=> 'title',
		));

		$this->addColumn('group_id', array(
			'header'	=> $this->__('Group'),
			'align'		=> 'left',
			'index'		=> 'group_id',
			'type'		=> 'options',
			'options'	=> $this->_getGroups(),
		));

		$this->addColumn('is_enabled', array(
			'header'	=> Mage::helper('wizard')->__('Enabled'),
			'width'		=> '90px',
			'index'		=> 'is_enabled',
			'type'		=> 'options',
			'options'	=> array(
				1 => $this->__('Enabled'),
				0 => $this->__('Disabled'),
			),
		));

		$this->addColumn('action',
				array(
				'header'	=>	Mage::helper('wizard')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
						array(
								'caption'	=> Mage::helper('wizard')->__('Edit'),
								'url'		=> array('base'=> '*/*/edit'),
								'field'		=> 'id'
						),
						array(
								'caption'	=> Mage::helper('wizard')->__('Delete'),
								'url'		=> array('base'=> '*/*/delete'),
								'field'		=> 'id'
						)
				),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system' => true,
		));

		return parent::_prepareColumns();
	}
	
	/**
	 * Retrieve an array of all of the stores
	 *
	 * @return array
	 */
	protected function _getGroups() {
		$groups = Mage::getResourceModel('wizard/group_collection');
		$options = array();

		foreach($groups as $group) {
			$options[$group->getId()] = $group->getTitle();
		}

		return $options;
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('slide_id');
		$this->getMassactionBlock()->setFormFieldName('slide');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> $this->__('Delete'),
			'url'  		=> $this->getUrl('*/*/massDelete'),
			'confirm' 	=> Mage::helper('catalog')->__('Are you sure?')
		));

		$statuses = array(
			0 => Mage::helper('wizard')->__('Disabled'),
			1 => Mage::helper('wizard')->__('Enabled')
		);
		
		$this->getMassactionBlock()->addItem('status', array(
				'label'		=> Mage::helper('wizard')->__('Change status'),
				'url'  		=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
				'confirm' 	=> Mage::helper('catalog')->__('Are you sure?'),
				'additional' => array(
						'visibility' => array(
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper('wizard')->__('Status'),
								'values' => $statuses
						)
				)
		));

		return $this;
	}

	/**
	 * Retrieve the URL for the row
	 */
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}