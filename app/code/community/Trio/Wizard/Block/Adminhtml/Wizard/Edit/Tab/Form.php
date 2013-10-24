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
                    /*
		$hosted_image = $fieldset->addField('hosted_image', 'select', array(
			'name'		=> 'hosted_image',
			'label'		=> Mage::helper('wizard')->__('Use External Image Hosting'),
			'note'		=> Mage::helper('wizard')->__('instead of uploading images you can host your images on a image hoster and just enter the link to the image and thumbnail'),
			'required'	=> true,
			'disabled' 	=> $this->_addOrEdit(),
			'values'	=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
		));

		$hosted_image_url = $fieldset->addField('hosted_image_url', 'text', array(
			'name'		=> 'hosted_image_url',
			'label'		=> $this->__('Hosted Image URL'),
			'title'		=> $this->__('Hosted Image URL')
		));

		$hosted_image_thumburl = $fieldset->addField('hosted_image_thumburl', 'text', array(
			'name'		=> 'hosted_image_thumburl',
			'label'		=> $this->__('Hosted Image Thumb URL'),
			'title'		=> $this->__('Hosted Image Thumb URL'),
			'note'		=> Mage::helper('wizard')->__('you can use the same URL as above but for performance reasons it\'s better to upload a seperate small thumbnail of this image, the thumbnails are used in carousels'),
		));
                */
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
                
                $data=null;
                 if ($wizard = Mage::registry('wizard_wizard')) {
			$data=$wizard->getData();
		}
                //var_dump($data);die;
                $fieldset->addField('scope', 'text', array(
                        'name'  =>'scope',
                        'label'	=> $this->__('Scope'),
                        'class' =>'requried-entry'
                        //, 'value' =>$data['scope']
                )); 

                $form->getElement('scope')->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/trio_wizard_tab_scope')->assign('data', $data)->assign('id_data','wizard_scope')
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

	/**
	 * Check if we are adding or editing
	 *
	 * @return bool
	 */
	public function _addOrEdit() {
		if($this->getRequest()->getParam('id')) { return true; } else { return false; }
	}

}