<?php
class Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("wizard_form", array("legend"=>Mage::helper("wizard")->__("Item information")));

						/*
						$fieldset->addField("wizard_id", "text", array(
						"label" => Mage::helper("wizard")->__("ID"),
						"name" => "wizard_id",
						));
						*/
						$fieldset->addField("status", "text", array(
						"label" => Mage::helper("wizard")->__("Status"),
						"name" => "status",
						));
					
						$fieldset->addField("title", "text", array(
						"label" => Mage::helper("wizard")->__("Title"),
						"name" => "title",
						));
						/*
						$fieldset->addField("code", "text", array(
						"label" => Mage::helper("wizard")->__("Code"),
						"name" => "code",
						));
						*/
						
									
						$fieldset->addField('image', 'image', array(
						'label' => Mage::helper('wizard')->__('Image'),
						'name' => 'image',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						/*
						//class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price extends Mage_Adminhtml_Block_Widget_Form
						
						$fieldset->addField('tier_price', 'text', array(
								'name'=>'tier_price',
								'class'=>'requried-entry',
								'value'=>$product->getData('tier_price')
						));

						$form->getElement('tier_price')->setRenderer(
							$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
						);
						*/
						/*
						//$product = Mage::registry('product');
						//$data =$this->getData();
						$fieldset->addField("scope", "text", array(
						"label" => Mage::helper("wizard")->__("Scope"),
						"name" => "scope",
						//'value'=>$product->getData('scope')
						));
						$fieldset->addField('label', 'text', array(
							'name' => 'label',
							'label' => 'Label',
						));
						$block=$this->getLayout()->createBlock('trio_wizard/adminhtml_wizard_edit_tab_scope');
						Mage::log(get_class($block).'Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Form   -> _prepareForm  11111 /n');
						//$form->getElement('scope')->setRenderer( $this->getLayout()->createBlock('adminhtml/wizard_edit_tab_scope_allow') );
						$form->getElement('label')->setRenderer($this->getLayout()->createBlock('trio_wizard/adminhtml_wizard_edit_tab_scope') );
						*/
						//$layout = Mage::getSingleton('core/layout');
						//$block  = $layout->createBlock('trio_wizard/adminhtml_wizard_edit_tab_scopetype');
						//Mage::log('Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Form   -> _prepareForm 11111111 /n');
						//Mage::getConfig()->getBlockClassName('trio_wizard/adminhtml_wizard_edit_tab_scopetype')
						
						$fieldset->addType('scope_type', Mage::getConfig()->getBlockClassName('adminhtml/trio_wizard_tab_scope'));
						//$fieldset->addType('multiselect_enabled', Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_scope'));
						
						$fieldset->addField('scope', 'scope_type', array(
							'name' => 'name-test',
							'label' => 'Scope',
							'values' => Mage::getModel('catalog/category_attribute_source_sortby')->getAllOptions(),
							'checkbox_label' => 'checkbox_label-test',
							'required' => true,
							));
						Mage::log('Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Form   -> _prepareForm 22222 /n');
						

				if (Mage::getSingleton("adminhtml/session")->getWizardData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getWizardData());
					Mage::getSingleton("adminhtml/session")->setWizardData(null);
				} 
				elseif(Mage::registry("wizard_data")) {
				    $form->setValues(Mage::registry("wizard_data")->getData());
				}
				return parent::_prepareForm();
		}
}
