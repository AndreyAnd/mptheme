<?php   
class Trio_Wizard_Block_Wizard extends Mage_Core_Block_Template{   
    
	public function __construct()
    {
        parent::__construct();
		$datasets=Mage::getModel('wizard/wizard')->getCollection();
        $this->setDatasets($datasets);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager')->setCollection($this->getDatasets());
        $this->setChild('pager', $pager);
        $this->getDatasets()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }



}