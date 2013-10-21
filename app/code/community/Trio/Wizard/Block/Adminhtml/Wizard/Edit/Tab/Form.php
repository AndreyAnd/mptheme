<?php
class Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();       
        $this->setForm($form);
        $data=null;
         if (Mage::getSingleton("adminhtml/session")->getWizardData())
        {
           $data=Mage::getSingleton("adminhtml/session")->getWizardData();
        } 
        elseif(Mage::registry("wizard_data")) {
            $data=Mage::registry("wizard_data")->getData();
        }
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
                        
                        $fieldset->addField("code", "text", array(
                        "label" => Mage::helper("wizard")->__("Code"),
                        "name" => "code",
                        ));
                        


                        $fieldset->addField('image', 'image', array(
                        'label' => Mage::helper('wizard')->__('Image'),
                        'name' => 'image',
                        'note' => '(*.jpg, *.png, *.gif)',
                        ));
                        
                        
                        $fieldset->addField('scope', 'text', array(
                                'name'=>'scope',
                                'class'=>'requried-entry',
                                'value'=>$data['scope']
                        )); 
                        
                        $form->getElement('scope')->setRenderer(
                            $this->getLayout()->createBlock('adminhtml/trio_wizard_tab_scope')->assign('data', $data)
                        );
                        
                        //Mage::log('Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Form   -> _prepareForm 1111111 /n');
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
                        Mage::log('Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Form   -> _prepareForm 22222 /n');
                        */

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
