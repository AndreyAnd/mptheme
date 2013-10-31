<?php
/**
 * @package	Wizard
 * @author     callmeandrey@gmail.com
 */

class Trio_Wizard_Block_Adminhtml_Group_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {

		$_model = Mage::registry('group_data');
		$form = new Varien_Data_Form();

		$this->setForm($form);

		$fieldset = $form->addFieldset('general_form', array('legend'=>Mage::helper('wizard')->__('General Information')));

		$title = $fieldset->addField('title', 'text', array(
			'name'		=> 'title',
			'label'		=> Mage::helper('wizard')->__('Title'),
			'required'	=> true,
			'class'		=> 'required-entry',
			'value'		=> $_model->getTitle()
		));

                $code = $fieldset->addField('code', 'text', array(
			'name'		=> 'code',
			'label'		=> Mage::helper('wizard')->__('Code'),
			'note'		=> Mage::helper('wizard')->__('a unique identifier group'),
			'required'	=> true,
                        'value'		=> $_model->getCode()
		));
        /*
		$position = $fieldset->addField('position', 'select', array(
			'name'		=> 'position',
			'label'		=> Mage::helper('wizard')->__('Position'),
			'required'	=> true,
			'values'	=> Mage::getSingleton('wizard/config_source_position')->toOptionArray(),
			'value'		=> $_model->getPosition()
		)); */

		$sort_order = $fieldset->addField('sort_order', 'text', array(
			'name'		=> 'sort_order',
			'label'		=> Mage::helper('wizard')->__('Sort Order'),
			'note'		=> Mage::helper('wizard')->__('set the sort order in case of multiple wizards on one page'),
			'required'	=> false,
			'value'		=> $_model->getSortOrder()
		));

		$is_active = $fieldset->addField('is_active', 'select', array(
			'name'		=> 'is_active',
			'label'		=> Mage::helper('wizard')->__('Is Enabled'),
			'required'	=> true,
			'values'	=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
			'value'		=> $_model->getIsActive()
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$stores = $fieldset->addField('stores', 'multiselect', array(
				'name'		=> 'stores[]',
				'label'		=> Mage::helper('wizard')->__('Visible In'),
				'required'	=> true,
				'values'	=> Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
				'value'		=> $_model->getStoreId()
			));
		}
		else {
			$stores = $fieldset->addField('stores', 'hidden', array(
				'name'		=> 'stores[]',
				'value'		=> Mage::app()->getStore(true)->getId()
			));
		}
               
		return parent::_prepareForm();
	}
        
        public function getAttributes()
        {
            $config    = Mage::getModel('eav/config');
            $storeId=Mage::app()->getStore(true)->getId();
            $attribute = $config->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'wizard_attributes');
            $options = Mage::getResourceModel('eav/entity_attribute_option_collection');
            $values  = $options->setAttributeFilter($attribute->getId())->setStoreFilter($storeId)->toOptionArray();
            //$values=Array ( [0] => Array ( [value] => 4 [label] => color ) [1] => Array ( [value] => 5 [label] => price ) [2] => Array ( [value] => 3 [label] => wizard_attributes ) ) 
            $arr=array();
            foreach($values as $val)
            {
               $arr[$val["value"]] = $val["label"];
            }
            return $values;          
            
        }
        
	public function returnWizardAniduration() {
		$_model = Mage::registry('group_data');
		if($_model->getWizardAniduration()) { return $_model->getWizardAniduration(); } else { return '600'; }
	}

	public function returnWizardWizardduration() {
		$_model = Mage::registry('group_data');
		if($_model->getWizardWizardduration()) { return $_model->getWizardWizardduration(); } else { return '7000'; }
	}

	public function returnThumbnailSize() {
		$_model = Mage::registry('group_data');
		if($_model->getThumbnailSize()) { return $_model->getThumbnailSize(); } else { return '200'; }
	}

	public function returnNavColor() {
		$_model = Mage::registry('group_data');
		if($_model->getNavColor()) { return $_model->getNavColor(); } else { return '#666666'; }
	}

}