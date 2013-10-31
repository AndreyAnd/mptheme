<?php
/**
 * @category	Trio
 * @package		Wizard
 */

class Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

	/**
	 * Retrieve Additional Element Types
	 *
	 * @return array
	*/
	protected function _getAdditionalElementTypes() {
		return array(
			'image' => Mage::getConfig()->getBlockClassName('wizard/adminhtml_wizard_helper_image')
		);
	}

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
                
		$form->setHtmlIdPrefix('wizard_');
		$form->setFieldNameSuffix('wizard');

		$this->setForm($form);

		$fieldset = $form->addFieldset('wizard_general', array('legend'=> $this->__('General Information')));

		$this->_addElementTypes($fieldset);

		$group_id = $fieldset->addField('group_id', 'select', array(
			'name'			=> 'group_id',
			'label'			=> $this->__('Group'),
			'title'			=> $this->__('Group'),
			'required'		=> true,
			'class'			=> 'required-entry',
			'values'		=> $this->_getGroups()
		));

		$title = $fieldset->addField('title', 'text', array(
			'name'		=> 'title',
			'label'		=> $this->__('Title'),
			'title'		=> $this->__('Title'),
			'required'	=> true,
			'class'		=> 'required-entry'
		));
		$view = $fieldset->addField('view','select', array(
			'name'		=> 'view',
			'label'		=> $this->__('View'),
			'title'		=> $this->__('View'),
			'required'	=> true,
			'values'	=> array('list'=>'list','grid' => 'grid')
		));
		//$_model = Mage::registry('group_data');
		$code = $fieldset->addField('code', 'select', array(
			'name'		=> 'code',
			'label'		=> Mage::helper('wizard')->__('Code'),
			'note'		=> Mage::helper('wizard')->__('a unique identifier attribute'),
			'required'	=> true,
			'values'	=> $this->getAttributes(),
                        //'value'		=> $_model->getCode()
		));
                                
                 
		$image = $fieldset->addField('image', 'image', array(
			'name'		=> 'image',
			'label'		=> $this->__('Image'),
			'title'		=> $this->__('Image'),
			'required'	=> false
		));
               
                /*
		$alt_text = $fieldset->addField('alt_text', 'text', array(
			'name'		=> 'alt_text',
			'label'		=> $this->__('ALT Text'),
			'title'		=> $this->__('ALT Text')
		));
		
		$url = $fieldset->addField('url', 'text', array(
			'name'		=> 'url',
			'label'		=> $this->__('URL'),
			'title'		=> $this->__('URL')
		));

		$url_target = $fieldset->addField('url_target', 'select', array(
			'name'		=> 'url_target',
			'label'		=> $this->__('URL Target'),
			'title'		=> $this->__('URL Target'),
			'values'	=> Mage::getSingleton('wizard/config_source_URLTarget')->toOptionArray()
		));

		$html = $fieldset->addField('html', 'editor', array(
			'name'		=> 'html',
			'label'		=> $this->__('Description'),
			'title'		=> $this->__('Description'),
			'wysiwyg'	=> true,
			'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig()
		));
                */
		$sort_order = $fieldset->addField('sort_order', 'text', array(
			'name'		=> 'sort_order',
			'label'		=> $this->__('Sort Order'),
			'title'		=> $this->__('Sort Order'),
			'class'		=> 'validate-digits'
		));

		$is_enabled = $fieldset->addField('is_enabled', 'select', array(
			'name' => 'is_enabled',
			'title' => $this->__('Enabled'),
			'label' => $this->__('Enabled'),
			'required' => true,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
		));
                
                //------------------------------------
                
                //$data=null;
                 //if ($wizard = Mage::registry('wizard_wizard')) {  $data=$wizard->getData(); }
                //var_dump($data);die;
                $fieldset->addField('scope', 'text', array(
                        'name'  =>'scope',
                        'label'	=> $this->__('Scope'),
                        'class' =>'requried-entry'
                        //, 'value' =>$data['scope']
                )); 

                $form->getElement('scope')->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/trio_wizard_tab_scope')->assign('id_data','wizard_scope')
                );
                /*
                $fieldset->addType('scope_type', Mage::getConfig()->getBlockClassName('adminhtml/trio_wizard_tab_scope'));
                //$fieldset->addType('multiselect_enabled', Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_scope'));

                $fieldset->addField('scope', 'scope_type', array(
                        'name' => 'name-test',
                        'label' => 'Scope',
                        'values' => Mage::getModel('catalog/category_attribute_source_sortby')->getAllOptions(),
                        'checkbox_label' => 'checkbox_label-test',
                        'required' => true,
                        ));
               */ 
                

		if ($wizard = Mage::registry('wizard_wizard')) {
			$form->setValues($wizard->getData());
		}

		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
		/*
		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                        ->addFieldMap($hosted_image->getHtmlId(), $hosted_image->getName())
                        ->addFieldMap($hosted_image_url->getHtmlId(), $hosted_image_url->getName())
			->addFieldMap($hosted_image_thumburl->getHtmlId(), $hosted_image_thumburl->getName())
			->addFieldMap($image->getHtmlId(), $image->getName())
                        ->addFieldDependence(
                        $image->getName(),
                        $hosted_image->getName(),
                        0
                    )
                    ->addFieldDependence(
                        $hosted_image_url->getName(),
                        $hosted_image->getName(),
                        1
                    )
                    ->addFieldDependence(
                        $hosted_image_thumburl->getName(),
                        $hosted_image->getName(),
                        1
                    )              
                );*/

		return parent::_prepareForm();
	}

	/**
	 * Retrieve an array of all of the stores
	 *
	 * @return array
	 */
	protected function _getGroups() {
		$groups = Mage::getResourceModel('wizard/group_collection');
		$options = array('' => $this->__('-- Please Select --'));

		foreach($groups as $group) {
			$options[$group->getId()] = $group->getTitle();
		}

		return $options;
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
               //$arr[$val["value"]] = $val["label"];
               $arr[$val["label"]] = $val["label"]; 
            }
            //print_r( Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray() );
            //print_r($values);die();
            return $arr;  
        }

	/**
	 * Check if we are adding or editing
	 *
	 * @return bool
	 */
	public function _addOrEdit() {
		if($this->getRequest()->getParam('id')) { return true; } else { return false; }
	}

}