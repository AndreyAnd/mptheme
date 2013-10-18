<?php
class Trio_Trio_Model_Observer
{
	public function hookTo_controllerActionPostdispatchAdminhtmlSystemConfigSave()
	{
		$section = Mage::app()->getRequest()->getParam('section');
		if ($section == 'trio_settings')
		{
			$websiteCode = Mage::app()->getRequest()->getParam('website');
			$storeCode = Mage::app()->getRequest()->getParam('store');
		
			Mage::getSingleton('trio/cssconfig_generator')->generateCss('settings',   $websiteCode, $storeCode);
		}
		elseif ($section == 'trio_design')
		{
			$websiteCode = Mage::app()->getRequest()->getParam('website');
			$storeCode = Mage::app()->getRequest()->getParam('store');
			
			Mage::getSingleton('trio/cssconfig_generator')->generateCss('design', $websiteCode, $storeCode);
		}
	}
	
	/**
     * After store view is saved
     */
	public function hookTo_storeEdit(Varien_Event_Observer $observer)
	{
		$store = $observer->getEvent()->getStore();
		$storeCode = $store->getCode();
		$websiteCode = $store->getWebsite()->getCode();
		
		Mage::getSingleton('trio/cssconfig_generator')->generateCss('settings', $websiteCode, $storeCode);
		Mage::getSingleton('trio/cssconfig_generator')->generateCss('design', $websiteCode, $storeCode);
	}
}
