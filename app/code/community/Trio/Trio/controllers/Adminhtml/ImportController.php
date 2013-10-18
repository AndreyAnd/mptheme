<?php 
    class Trio_Trio_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action{ 
        public function indexAction() {
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/trio_settings/"));
        }
        public function blocksAction() {
            $isoverwrite = Mage::helper('trio')->getCfg('install/overwrite_blocks');
            Mage::getSingleton('trio/import_cms')->importCms('cms/block', 'blocks', $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/trio_settings/"));
        }
        public function pagesAction() {
            $isoverwrite = Mage::helper('trio')->getCfg('install/overwrite_pages');
            Mage::getSingleton('trio/import_cms')->importCms('cms/page', 'pages', $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/trio_settings/")); 
        }
    }
?>