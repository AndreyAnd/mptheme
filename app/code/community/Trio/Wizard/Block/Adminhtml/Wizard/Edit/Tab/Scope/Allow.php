<?php

class Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scope_Allow
     extends Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scope_Abstract 
    
{

    /**
     * Initialize block
     */
    public function __construct()
    {
        //$this->setTemplate('qquoteadv/catalog/product/edit/allow.phtml');
		//echo "7777777777777777";die('Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scope_Allow   -> __construct()');
		Mage::log('Trio_Wizard_Block_Adminhtml_Wizard_Edit_Tab_Scope_Allow   -> __construct() /n');die();
		$this->setTemplate('wizard/scope/allow.phtml');// app/design/adminhtml/default/default/template/wizard/scope/allow.phtml
    }

    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    protected function _getInitialCustomerGroups()
    {
        return array(); // array(Mage_Customer_Model_Group::CUST_GROUP_ALL => Mage::helper('catalog')->__('ALL GROUPS'));
    }

   
    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('qquoteadv')->__('Add Group'),
                'onclick' => 'return quoteGroupControl.addItem()',
                'class' => 'add'
            ));
        $button->setName('add_group_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

}
