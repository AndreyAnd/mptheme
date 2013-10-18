<?php
class Trio_Wizard_Model_Mysql4_Wizard extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("wizard/wizard", "wizard_id");
    }
}