<?php
class Trio_Filterproducts_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function bestsellersAction()
    {

		$url = Mage::getUrl('no-route'); 

		$enable = Mage::getStoreConfig('filterproducts/bestseller/active');
		if($enable != 1) 
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}	
		else
		{
		    $this->loadLayout(); 
		    $this->getLayout()->getBlock('head')->setTitle('Besesellers');    
		    $this->renderLayout();
		}
    }
	public function featuredAction()
    {
        $block = $this->getLayout()->createBlock(
            'core/template',
            'featured_product',
            array('template' => 'trio/filterproducts/featured.phtml')
        );
        echo $block->toHtml(); 
    }
	public function newproductAction()
    {
        $block = $this->getLayout()->createBlock(
            'core/template',
            'new_product',
            array('template' => 'trio/filterproducts/newproduct.phtml')
        );
        echo $block->toHtml(); 
    }
	
	public function specialAction()
    {
        $block = $this->getLayout()->createBlock(
            'core/template',
            'special_product',
            array('template' => 'trio/filterproducts/special.phtml')
        );
        echo $block->toHtml(); 
    }
}