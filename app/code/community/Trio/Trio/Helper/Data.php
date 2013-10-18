<?php

class Trio_Trio_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    protected $_texturePath;
    
    public function __construct()
    {
        $this->_texturePath = 'wysiwyg/trio/texture/default/';
    }

    public function getCfgGroup($group, $storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('trio/' . $group, $storeId);
        else
            return Mage::getStoreConfig('trio/' . $group);
    }
    
    public function getCfgSectionDesign($storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('trio_design', $storeId);
        else
            return Mage::getStoreConfig('trio_design');
    }

    public function getCfgSectionSettings($storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('trio_settings', $storeId);
        else
            return Mage::getStoreConfig('trio_settings');
    }
    
    public function getTexturePath()
    {
        return $this->_texturePath;
    }

    public function getCfg($optionString)
    {
        return Mage::getStoreConfig('trio_settings/' . $optionString);
    }
}
