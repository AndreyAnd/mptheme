<?php
class Ophirah_Not2Order_Helper_Data extends Mage_Core_Helper_Data {

    public function getShowPrice( $_product ) {

        $enabled = Mage::getStoreConfig('not2order/general/enabled');
        if ($enabled != '1') { return; }

        $_product = Mage::getModel('catalog/product')->load( $_product->getId() );
        $loggedIn = Mage::getModel('customer/session')->isLoggedIn();

        if (   !$loggedIn && ($_product->getHidePrice() == '2' || $_product->getHidePrice() == '1')
            || $loggedIn && $_product->getHidePrice() == '1') {
            return false;
        }

        return true;
    }

    public function useTemplates( $name = '', $path = '' ) {

        $enabled = Mage::getStoreConfig('not2order/general/enabled');

        $layout = Mage::app()->getLayout();
        $currentTemplate = $layout->getBlock( $name )->getTemplate();

        $useTemplates = Mage::getStoreConfig('not2order/general/usetemplates');

        if ($useTemplates == '1' && $enabled == '1') {
            return $path;
        }

        return $currentTemplate;
    }
}