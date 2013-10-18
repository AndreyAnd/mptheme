<?php

class Trio_Wizard_Block_Adminhtml_Wizard_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("wizardGrid");
				$this->setDefaultSort("wizard_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("wizard/wizard")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				/*
				$this->addColumn("wizard_id", array(
				"header" => Mage::helper("wizard")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "wizard_id",
				));
                */
				$this->addColumn("status", array(
				"header" => Mage::helper("wizard")->__("Status"),
				"index" => "status",
				));
				$this->addColumn("title", array(
				"header" => Mage::helper("wizard")->__("Title"),
				"index" => "title",
				));
				$this->addColumn("code", array(
				"header" => Mage::helper("wizard")->__("Code"),
				"index" => "code",
				));
				/*
				$this->addColumn("scope", array(
				"header" => Mage::helper("wizard")->__("Scope"),
				"index" => "scope",
				));
				*/
				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('wizard_id');
			$this->getMassactionBlock()->setFormFieldName('wizard_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_wizard', array(
					 'label'=> Mage::helper('wizard')->__('Remove Wizard'),
					 'url'  => $this->getUrl('*/adminhtml_wizard/massRemove'),
					 'confirm' => Mage::helper('wizard')->__('Are you sure?')
				));
			return $this;
		}
			

}