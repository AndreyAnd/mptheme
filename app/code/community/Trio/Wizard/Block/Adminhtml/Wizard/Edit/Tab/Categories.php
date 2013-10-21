<?php
class Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget_Form
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
        $fieldset = $form->addFieldset("wizard_form", array("legend"=>Mage::helper("wizard")->__("Categories")));

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


