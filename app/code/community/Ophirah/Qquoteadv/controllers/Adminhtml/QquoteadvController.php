<?php
final class Ophirah_Qquoteadv_Adminhtml_QquoteadvController
    extends Mage_Adminhtml_Controller_Action
{

    CONST XML_PATH_QQUOTEADV_REQUEST_PROPOSAL_EMAIL_TEMPLATE = 'qquoteadv/emails/proposal';
    CONST EXPORT_FOLDER_PATH = '/var/qquoteadv_export/';
    
    /*
     * CUSTOMER GRID
     * 
     * 
     */    
    
        protected function _initCustomer($idFieldName = 'id')
    { 
            
        $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    
    /**
     * Customer quotes grid
     *
     */
    
    public function quotesAction()
    {          
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    
    /*
     * CUSTOMER GRID
     * 
     * 
     */     
    
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/qquoteadv')
                ->_addBreadcrumb($this->__('Items Manager'), $this->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('qquoteadv/qqadvcustomer')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('qquote_data', $model);
            $skinUrl =  Mage::getDesign()->getSkinUrl();
            
            
            $this->loadLayout();
            $this->_setActiveMenu('qquoteadv/items');

            $head = $this->getLayout()->getBlock('head');
            $head->setCanLoadExtJs(true);
            $access = $this->getAccessLevel();
            if (is_null($access) || $this->isTrialVersion()) {
                $msgUpgrade = $this->getMsgToUpgrade();
                $this->_addContent($this->getLayout()->createBlock('core/text', 'example-block')
                                ->setText($msgUpgrade));
            }
   
            $this->_addContent($this->getLayout()->createBlock('qquoteadv/adminhtml_qquoteadv_edit'))
                    ->_addLeft($this->getLayout()->createBlock('qquoteadv/adminhtml_qquoteadv_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {

        $this->loadLayout();

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('qquoteadv/adminhtml_qquoteadv_edit'));

        $this->renderLayout();
        //$this->_forward('edit');
    }

    protected function _sendProposalEmail($customerId, $realQuoteadvId) {
        try {
            $customer = Mage::getModel('customer/customer')->load($customerId);

            $res = $this->sendEmail(array('email' => $customer->getEmail(), 'name' => $customer->getName()));

            if (empty($res)) {
                $message = $this->__("Qquote proposal email was't sent to the client for quote #%s", $realQuoteadvId);
                Mage::getSingleton('adminhtml/session')->addError($message);
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Email was sent to client'));
                Mage::helper('qquoteadv')->sentAnonymousData('proposal', 'b');
            }
        } catch (Exception $e) {
            $message = $this->__("Qquote proposal email was't sent to the client for quote #%s", $realQuoteadvId);
            Mage::log($e->getMessage());
            Mage::getSingleton('adminhtml/session')->addError($message);
            $this->_redirect('*/*/');
            return;
        }
    }
   
    public function saveAction() {     	
    
        if(!Mage::helper('qquoteadv')->validLicense('create-edit-admin')){
                Mage::getSingleton('adminhtml/session')->addError(__("Please upgrade to Cart2Quote Standard or higher to use this feature"));
                $this->_redirectReferer();
                return;
        }

        if ($data = $this->getRequest()->getPost()) {
            try {
                if (is_array($data['product']) && count($data['product']) > 0) {
                    
                    if ($quoteId = (int) $this->getRequest()->getParam('id')) {
                        $_quoteadv = Mage::getModel('qquoteadv/qqadvcustomer')->load($quoteId);
                        $baseCurrency = Mage::app()->getBaseCurrencyCode();
                        $currencyCode = $_quoteadv->getData('currency');
                        $rate = $_quoteadv->getData('base_to_quote_rate');
                       
                        $errors = array();
                        $prodId_prev = 0;
                        foreach ($data['product'] as $id => $arr) {                        
                            
                            $price = $arr['price'];
                            $qty = $arr['qty'];
                            $model = Mage::getModel('qquoteadv/requestitem')->load($id);
                            
                            // store original current price for tier requests
                            $orgCurPrice = $model->getData('original_cur_price');
                            $prodId = $model->getData('quoteadv_product_id');

                            if($orgCurPrice == 0 && $prodId_prev == $prodId) {
                               
                                $orgCurPrice = $prev_orgCurPrice;
                                $model->setOriginalCurPrice($orgCurPrice);
                            } elseif($prodId_prev != $prodId) {

                                $prev_orgCurPrice = $orgCurPrice;
                                $prodId_prev = $prodId;
                            }
                            
                            $productId = $model->getProductId();                            
                            $check = Mage::helper('qquoteadv')->isQuoteable( $productId , $qty);
                            if($check->getHasErrors()){
                                $errors = $check->getErrors();
                                $this->_redirectErr($errors);
                                return;
                            }
                            
                            
                            try {
                                
                                
                                $model->setOwnerCurPrice($price);
                                $basePrice = $price / $rate;
                                $model->setOwnerBasePrice($basePrice);
                                $model->save();
                            } catch (Exception $e) {
                                $errors[] = $this->__("Item #%s was't updated", $id);
                            }
                        }                        
                        
                        if (is_array($data['requestedproduct']) && count($data['requestedproduct']) > 0) {

                            $errors = array();

                            foreach ($data['requestedproduct'] as $id => $arr) {

                                //if($client_request = $arr['client_request']){
                                $client_request = $arr['client_request'];
                                $comment = trim(strip_tags($client_request));

                                $item = Mage::getModel('qquoteadv/qqadvproduct')->load($id);

                                try {
                                  $item->setClientRequest($comment);
                                  $item->save();                                
                                } catch (Exception $e) {
                                    $errors[] = $this->__("Item #%s was't updated", $model->getProductId());
                                }

                            }
                        }

                        //FILE UPLOAD
                        if($fileTitle = $this->getRequest()->getParam('file_title')){ 
                        	
                          $_quoteadv->setFileTitle($fileTitle);
                         
                          if($pathInfo = $this->getRequest()->getParam('path_info')){ 
	                          
                          	  if($pathInfo=='file' && $filePath = $this->fileUpload() ){                           
	                           	$_quoteadv->setPath($filePath);
	                          }
	                          
	                          elseif($pathInfo=='url' && $value = $this->getRequest()->getParam('url_path')){
	                           
                                        if (!Mage::helper('qquoteadv')->isValidHttp($value)) {
                                            //Mage::throwException(Mage::helper('core')->__('The %s you entered is invalid. Please make sure that it follows "http://domain.com/" format.', $value));						            
                                                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('The %s you entered is invalid. Please make sure that it follows "http://domain.com/" format.', $value));
                                                }else{
                                                $_quoteadv->setPath($value);                             
                                        }						        
	                           }
	                        }
                        } 
                        
                        
                        if ($this->getRequest()->getParam('back')) {
                            $_quoteadv->setStatus(Ophirah_Qquoteadv_Model_Status::STATUS_PROPOSAL);
                        } else {
                            //# update status
                            $_quoteadv->setStatus(Ophirah_Qquoteadv_Model_Status::STATUS_PROPOSAL_SAVED);
                        }

                        if ($client_request = $this->getRequest()->getParam('client_request')) {
                            $comment = trim(strip_tags($client_request));
                            $_quoteadv->setClientRequest($comment);
                        }
                        
                        if ($expiry = $this->getRequest()->getParam('expiry') and preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $expiry)) {
                            $m = substr($expiry, 0, 2);
                            $d = substr($expiry, 3, 2);
                            $y = substr($expiry, 6, 4);
                            $expiryFormatted = $y."-".$m."-".$d;
                            $_quoteadv->setExpiry($expiryFormatted);
                        }
                       
                        $no_expiry = ($this->getRequest()->getParam('no_expiry') && $this->getRequest()->getParam('no_expiry')=="on")? 1:0;
                        $_quoteadv->setNoExpiry($no_expiry);
                        
                         if ($assignedTo = $this->getRequest()->getParam('assigned_to') ) {
                             
                          $saveas =  Mage::getModel('admin/user')->load($assignedTo); 
                          if(!$saveas->getUserId()){
                               Mage::getSingleton('adminhtml/session')->addError($this->__('Could not find user with email address: %s', $email));
                               $saveas = Mage::getSingleton('admin/session')->getUser();
                          }
                        }else{
                          $saveas = Mage::getSingleton('admin/session')->getUser();
                        }
                        
                        
                        $_quoteadv->setUserId($saveas->getUserId());

                        //#save shipping price
                        $shippingType = $this->getRequest()->getPost("shipping_type", "");
                        $_quoteadv->setShippingType($shippingType);

                        $shippingPrice = $this->getRequest()->getPost("shipping_price", -1);
                        
                        $_quoteadv->setShippingPrice($shippingPrice);
                        $shippingBasePrice = $shippingPrice / $rate;
                        $_quoteadv->setShippingBasePrice($shippingBasePrice);
                        
                        $_quoteadv->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
                        
                        $userId = $_quoteadv->getUserId(); 
                        if(empty($userId)) {
                          $adm_id = Mage::getSingleton('admin/session')->getUser()->getId();
                          $_quoteadv->setUserId($adm_id);
                        }else{
                          $model = Mage::getModel('admin/user')->load($userId);
                          //#admin is not exists
                          if(!$model->getId() && $id) {
                            $adm_id = Mage::getSingleton('admin/session')->getUser()->getId();
                            $_quoteadv->setUserId($adm_id);                            
                          }                          
                        }

                        // Unset data from sales rep if not allowed
                        if(!Mage::getSingleton('admin/session')->isAllowed('sales/qquoteadv/salesrep'))
                        {
                            $_quoteadv->setUserId($_quoteadv->getOrigData('user_id'));
                        }
                        
                        try{
                            $shippingType = $_quoteadv->getShippingType();
                            if( $shippingType == "I" ||  $shippingType == "O" ){
                                 $_quoteadv->setAddressShippingMethod('flatrate_flatrate');
                                 $_quoteadv->save();
                            }
                            
                            $_quoteadv->collectTotals();

                            $_quoteadv->save();
                            
                            Mage::helper('qquoteadv')->sentAnonymousData('save','b');
                        }catch(Exception $e){ 
                            Mage::log($e->getMessage()); 
                        }

                      
                        if ($this->getRequest()->getParam('back')) {

                            $realQuoteadvId = $_quoteadv->getIncrementId() ? $_quoteadv->getIncrementId() : $_quoteadv->getId();

                            //#send Proposal email
                            if ($customerId = $_quoteadv->getCustomerId())
                                
                                Mage::register('qquoteadv', $_quoteadv);
                                $this->_sendProposalEmail($customerId, $realQuoteadvId);
                                Mage::unregister('qquoteadv');
                        }
                    }
                }
                
				if(count(Mage::getSingleton('adminhtml/session')->getMessages()->getErrors())) {
					Mage::getSingleton('adminhtml/session')->addNotice($this->__('Quote was saved with errors'));
                }else{
                	Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Quote was successfully saved'));
                }
				Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                if($data['redirect2neworder']==1){
                    $this->_redirect('*/*/convert/', array('id' => $quoteId, 'q2o_serial' => serialize($data['q2o'])));
                }elseif ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $quoteId));                                             
                }else{
                    $this->_redirect('*/*/edit', array('id' => $quoteId));                
                }
                
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {

        $id = (int) $this->getRequest()->getParam('id');

        if ($id > 0) {
            try {
                $model = Mage::getModel('qquoteadv/qqadvcustomer');

                $model->setId($id)
                        ->setStatus(Ophirah_Qquoteadv_Model_Status::STATUS_CANCELED) //STATUS_REJECTED
                        ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Quote was successfully canceled'));
                Mage::helper('qquoteadv')->sentAnonymousData('cancel','b');
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $qquoteIds = $this->getRequest()->getParam('qquote');
        if (!is_array($qquoteIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($qquoteIds as $qquoteId) {
                    $qquote = Mage::getModel('qquoteadv/qqadvcustomer')->load($qquoteId);
                    $qquote->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__(
                                'Total of %d record(s) were successfully deleted', count($qquoteIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $qquoteIds = $this->getRequest()->getParam('qquote');
        if (!is_array($qquoteIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($qquoteIds as $qquoteId) {
                    $qquote = Mage::getSingleton('qquoteadv/qqadvcustomer')
                                    ->load($qquoteId)
                                    ->setStatus($this->getRequest()->getParam('status'))
                                    ->setIsMassupdate(true)
                                    ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($qquoteIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'qquote.csv';
        $content = $this->getLayout()->createBlock('qquoteadv/adminhtml_qquote_grid')
                        ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'qquote.xml';
        $content = $this->getLayout()->createBlock('qquoteadv/adminhtml_qquote_grid')
                        ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    /**
     * Send email to client to informing about the quote proposition
     * @param array $params customer address
     */
    public function sendEmail($params) {
        $admin = Mage::getModel("admin/user")->getCollection()->getData();

        //Create an array of variables to assign to template
        $vars = array();

        $this->quoteId = (int) $this->getRequest()->getParam('id');
        /* @var $_quoteadv Ophirah_Qquoteadv_Model_Qqadvcustomer */
        $_quoteadv = Mage::getModel('qquoteadv/qqadvcustomer')->load($this->quoteId);

        $vars['quote'] = $_quoteadv;
        $vars['customer'] = Mage::getModel('customer/customer')->load($_quoteadv->getCustomerId());

        $template = Mage::getModel('core/email_template');

	$quoteadv_param = Mage::getStoreConfig('qquoteadv/emails/proposal', $_quoteadv->getStoreId());
        if ($quoteadv_param) {
            $templateId = $quoteadv_param;
        } else {
            $templateId = self::XML_PATH_QQUOTEADV_REQUEST_PROPOSAL_EMAIL_TEMPLATE;
        }
				
				// get locale of quote sent so we can sent email in that language	
				$storeLocale = Mage::getStoreConfig('general/locale/code', $_quoteadv->getStoreId());
				
        if (is_numeric($templateId)) {
            $template->load($templateId);
        } else {
            $template->loadDefault($templateId, $storeLocale);
        }

        $vars['attach_pdf'] = $vars['attach_doc'] = false;

        //Create pdf to attach to email

        if (Mage::getStoreConfig('qquoteadv/attach/pdf', $_quoteadv->getStoreId())) {
            $pdf = Mage::getModel('qquoteadv/pdf_qquote')->getPdf($_quoteadv);
            $realQuoteadvId = $_quoteadv->getIncrementId() ? $_quoteadv->getIncrementId() : $_quoteadv->getId();
			try{
				$file = $pdf->render();
				$name = Mage::helper('qquoteadv')->__('Price_proposal_%s', $realQuoteadvId);
				$template->getMail()->createAttachment($file,'application/pdf',Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $name.'.pdf');
				$vars['attach_pdf'] = true;

			}catch(Exception $e){ Mage::log($e->getMessage()); }
			
        }

        if ($doc = Mage::getStoreConfig('qquoteadv/attach/doc', $_quoteadv->getStoreId())) { 
        	$pathDoc =  Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA). DS .'quoteadv'. DS .$doc;         	 
        	try{
                $file = file_get_contents($pathDoc);

                $info = pathinfo($pathDoc);
                //$extension = $info['extension']; 
                $basename = $info['basename'];
                $template->getMail()->createAttachment($file, '' ,Zend_Mime::DISPOSITION_ATTACHMENT,Zend_Mime::ENCODING_BASE64,$basename); 
                $vars['attach_doc'] = true;  
            }catch(Exception $e){ Mage::log($e->getMessage()); }
        }
        $remark = Mage::getStoreConfig('qquoteadv/general/qquoteadv_remark', $_quoteadv->getStoreId());
        if ( $remark ) {
            $vars['remark'] = $remark;
        }
        
        $adm_name = $this->getAdminName($_quoteadv->getUserId()); 
        $adm_name = trim($adm_name);        
        if ( empty($adm_name)) { $adm_name = $this->getAdminName(Mage::getSingleton('admin/session')->getUser()->getId()); } 
        if ( !empty($adm_name)) {
           $vars['adminname'] = $adm_name;
        }        

        $subject = $template['template_subject'];

        $vars['link'] = Mage::getUrl("qquoteadv/view/view/", array('id' => $this->quoteId));

	$sender = $_quoteadv->getEmailSenderInfo();
        $template->setSenderName($sender['name']);
        $template->setSenderEmail($sender['email']);

        $template->setTemplateSubject($subject);
	$bcc = Mage::getStoreConfig('qquoteadv/emails/bcc', $_quoteadv->getStoreId());
        if ($bcc) {
            $bccData = explode(";", $bcc);
            $template->addBcc($bccData);
        }

        if((bool) Mage::getStoreConfig('qquoteadv/emails/send_linked_sale_bcc', $_quoteadv->getStoreId())) {
            $template->addBcc(Mage::getModel('admin/user')->load($_quoteadv->getUserId())->getEmail());
        }

        $template->setDesignConfig(array('store' => $_quoteadv->getStoreId()));

        /**
         * Opens the qquote_request.html, throws in the variable array
         * and returns the 'parsed' content that you can use as body of email
         */
        $processedTemplate = $template->getProcessedTemplate($vars);

        /*
         * getProcessedTemplate is called inside send()
         */
        $res = $template->send($params['email'], $params['name'], $vars);

        return $res;
    }
    
    
    /*
     * Add quote comment action
     */

    public function addCommentAction() {
        if ($qquoteadv = $this->_initQuoteadv()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');

                $comment = trim(strip_tags($data['comment']));

                //$qquoteadv->save();

                $this->loadLayout('empty');
                $this->renderLayout();
            } catch (Mage_Core_Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $e->getMessage(),
                );
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Can not add quote history.')
                );
            }
            if (is_array($response)) {
                $response = Zend_Json::encode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    /**
     * Initialize qquoteadv model instance
     *
     * @return Quote || false
     */
    protected function _initQuoteadv() {
        $id = $this->getRequest()->getParam('quote_id');
        $qquoteadv = Mage::getModel('qquoteadv/qqadvcustomer')->load($id);

        if (!$qquoteadv->getId()) {
            $this->_getSession()->addError($this->__('This quote no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        Mage::register('qquote_data', $qquoteadv);

        return $qquoteadv;
    }

    public function pdfqquoteadvAction() {
        $quoteadvId = $this->getRequest()->getParam('id');
        $flag = false;
        if (!empty($quoteadvId)) {
            //foreach ($ids as $quoteadvId) {
            $_quoteadv = Mage::getModel('qquoteadv/qqadvcustomer')->load($quoteadvId);
			$_quoteadv->collectTotals();
            $_quoteadv->save();
                         

            $quoteItems = Mage::getModel('qquoteadv/qqadvproduct')->getCollection()
                            ->addFieldToFilter('quote_id', $quoteadvId)
                            ->load();

            if ($quoteItems->getSize()) {
                $flag = true;
                if (!isset($pdf)) {
                    $pdf = Mage::getModel('qquoteadv/pdf_qquote')->getPdf($_quoteadv);
                } else {
                    $pages = Mage::getModel('qquoteadv/pdf_qquote')->getPdf($quoteItems);
                    $pdf->pages = array_merge($pdf->pages, $pages->pages);
                }
            }
            //}
            if ($flag) {
				$realQuoteadvId = $_quoteadv->getIncrementId();
                $fileName = Mage::helper('qquoteadv')->__('Price_proposal_%s', $realQuoteadvId);

                return $this->_prepareDownloadResponse($fileName . '.pdf', $pdf->render(), 'application/pdf');
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected quotes'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }
    
    function isTrialVersion(){
        return Mage::helper('qquoteadv')->isTrialVersion();
    }

    function getMsgToUpgrade($updateMsg=false) {
        
         
        
        $msg = '
        <style>

.leightbox1, .leightboxlink   {
	background-color:#FFFFFF;
	border:2px solid #B8B8B8;
	color:#0A263C;
	display:none;
	font:25px Arial,sans-serif;
	left:30%;
	margin:0;
	overflow:auto;
	padding:0;
	position:absolute;
	text-align:left;
	top:25%;
	width:550px;
	min-height:150px;
	z-index:1001;
}
#overlay, #overlaylink{
	display:none;
	position:absolute;
	top:0;
	left:0;
	width:100%;
	height:200%;
	z-index:1000;
	background-color:#333;
	-moz-opacity: 0.8;
	opacity:.80;
	filter: alpha(opacity=80);
}

</style>

<script type="text/javascript">
function prepareIE(height, overflow)
{
bod = document.getElementsByTagName(\'body\')[0];
bod.style.height = height;
bod.style.overflow = overflow;

htm = document.getElementsByTagName(\'html\')[0];
htm.style.height = height;
htm.style.overflow = overflow;
}

function initMsg() {
	bod 				= document.getElementsByTagName(\'body\')[0];
	overlay 			= document.createElement(\'div\');
	overlay.id			= \'overlay\';
	bod.appendChild(overlay);
	$(\'overlay\').style.display = \'block\';
	$(\'lightbox1\').style.display = \'block\';
	prepareIE("auto", "auto");
}

function hideBox() {
    $(\'lightbox1\').style.display = \'none\';
    $(\'overlay\').style.display = \'none\';
}

</script>';
        $msg.='
<div id="lightbox1" class="leightbox1" style="display:none;">
<div >';


   
$headerText = "";
$onClick = "";
if($this->isTrialVersion() && $this->hasExpired()) {
	
	$text =  Mage::helper('qquoteadv')->_expiryText;
        $onClick = 'history.back()';
        $headerText = $this->__('Your Trial has expired');
        
	$btn1 = '<button target="_blank" class="button button1" title="Upgrade" onclick="'.$onClick.'">'.$this->__('No thanks I will use the free version').'</button>';
        $btn2 = '<button target="_blank" class="button button2" title="Request a license" onclick="document.location.href=\'http://www.cart2quote.com/pricing-magento-quotation-module.html?utm_source=Customer%2Bwebsite&utm_medium=license%2Bpopup&utm_campaign=Trial%2BVersion\'">'.$this->__('Yes take me there').'</button> ';
        $smallPrint = $this->__('*Ordered Cart2Quote, but no license yet? <a href="https://cart2quote.zendesk.com/entries/20199692-cfg-request-a-license-key-for-cart2quote">Request your license number.</a>');
} elseif($this->isTrialVersion() && !$this->hasExpired()) { 
        $expiry = Mage::helper('qquoteadv')->_expiryDate;
        
        $now = now();
        $expiry = substr($expiry, 0,4)."-".substr($expiry, 4,2)."-".substr($expiry, 6,2);
        $diff = abs(strtotime($expiry) - strtotime($now));
        $days = floor( $diff/ (60*60*24));
        $headerText = $this->__('Thanks for trying Cart2Quote!');
        $daysToGo =   sprintf("%d", $days);
        $text = $this->__(Mage::helper('qquoteadv')->_trialText, $daysToGo);
        $onClick = 'hideBox()';
      	$btn1 = '<button target="_blank" class="button" title="Continue Trial" href="" onclick="'.$onClick.'">'.$this->__('Continue Trial').'</button>';
        $btn2 = '<button target="_blank" class="button button2" title="Purchase a license" onclick="document.location.href=\'http://www.cart2quote.com/pricing-magento-quotation-module.html?utm_source=Customer%2Bwebsite&utm_medium=license%2Bpopup&utm_campaign=Trial%2BVersion%2BExpired\'">'.$this->__('Purchase a License*').'</button> ';
        $smallPrint = $this->__('*If you already ordered a license, you should receive your serial shortly');
}

$msg .= '<div id="quoteadv-box-header">';
$msg .= $headerText;   

$msg .= '<a onclick="'.$onClick.'" id="quoteadv-box-header-close-btn"></a>';
$msg .= '</div>';
$msg.= '<div class="text" >'.$text.'</div>';
$msg.= $btn1;
$msg.= $btn2;
$msg.='<div class="smallprint" >'.$smallPrint.'</div>
</div>
</div><script type="text/javascript">document.observe(\'dom:loaded\', function(){
 initMsg();
});</script>';

        return $msg;
    }
    
   private function getAccessLevel() {
    return Mage::helper('qquoteadv')->getAccessLevel();	    
   }
   
   private function hasExpired() {
	     return Mage::helper('qquoteadv')->hasExpired();	    
   }
    
    private function fileUpload( ){
    	
    	$filePath =''; 
    	if(isset($_FILES['file_path']['name']) and (file_exists($_FILES['file_path']['tmp_name']))) {
		
		  try {
		    $uploader = new Varien_File_Uploader('file_path');
		
		    //$uploader->setAllowedExtensions(array('pdf', 'doc', 'jpg','jpeg','gif','png')); // or pdf or anything
		    $uploader->setAllowRenameFiles(true);
		    // setAllowRenameFiles(true) -> move your file in a folder the magento way
		    // setAllowRenameFiles(true) -> move your file directly in the $path folder
		
		    $uploader->setFilesDispersion(false);
		    $path = Mage::getBaseDir('media') . DS ;
		
		    $result = $uploader->save($path, $_FILES['file_path']['name']);
			
			if(isset($result['file'])) {
				$filePath = $result['file'];
			} else { 
		    	$filePath = $_FILES['file_path']['name'];
		    }
		
		  }catch(Exception $e) { 
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage()); 
		  	//throw new Exception($e); //die($e->getMessage());
		  }
		}
		
		return $filePath; 
    }
    
     /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve order create model
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }    
    
    protected function _convertQuoteItemsToOrder($quoteadvId, $requestedItems){
         //# build sql
        $resource = Mage::getSingleton('core/resource');
        $read= $resource->getConnection('core_read');
        $tblProduct     = $resource->getTableName('quoteadv_product');
        $tblRequestItem = $resource->getTableName('quoteadv_request_item');

        $sql =  "select * from $tblProduct p INNER JOIN $tblRequestItem i  
                            ON p.quote_id=i.quote_id 
                            AND i.quoteadv_product_id=p.id AND p.quote_id=$quoteadvId"; 
                
        if (count($requestedItems)) {
            $items = implode(",", $requestedItems);
            $sql.= " AND i.request_id IN($items)";
        }else{ return; }
                
        $data = Mage::getSingleton('core/resource') ->getConnection('core_read')->fetchAll($sql);

        //add items from quote to order
        foreach($data as $item){
            $productId = $item['product_id'];

            $product    = Mage::getModel('catalog/product')->load($productId);
            //observer will check customPrice after add item to card/quote
            
            Mage::register('customPrice', $item['owner_cur_price']);
           
            if($product->getTypeId() == 'bundle' ){
            	$attr = array(); 
                $attr[$productId] =  @unserialize($item['attribute']); 
                $attr[$productId]['qty'] = (int)$item['request_qty'];
                $this->_getOrderCreateModel()->addProducts($attr);                
                
            }else{
                $params     = @unserialize($item['attribute']);
                $params['qty'] = (int)$item['request_qty'];
            
                try{                                          
                      $this->_getOrderCreateModel()->addProduct($product, $params);
                }catch(Exception $e){                        
                      Mage::log($e->getMessage());
                }                   
            }
            
            Mage::unregister('customPrice');
        }   
        
        
    }
    /*
     * 
     * params
    (
      [id] => 64
      [q2o_serial] => array(109,110)
    )
     */
    
    public function convertAction() {

        $requestedItems = array();
        $quoteadvId = $this->getRequest()->getParam('id');
        $requestedItems = $this->getRequest()->getParam('q2o');
        if(empty($requestedItems)){
            $requestedItems = $this->getRequest()->getParam('q2o_serial'); 
             if(!empty($requestedItems)){
                $requestedItems = unserialize($requestedItems);
            }
        }
        
        if ($requestedItems) {
           foreach($requestedItems as $k=>$v){
             if(empty($v)){
               unset($requestedItems[$k]);
             }
           }                
        }        

        if (!empty($quoteadvId)) {            
            $_quoteadv = Mage::getModel('qquoteadv/qqadvcustomer')->load($quoteadvId);              
            $currencyCode =  $_quoteadv->getData('currency');
            $storeId    = $_quoteadv->getStoreId();
            $this->_getSession()->setStoreId((int) $storeId);

            $customerId = (int)$_quoteadv->getCustomerId();
            $this->_getSession()->setCustomerId($customerId);
			
			
            // empty the quote before adding the items
             $this->_getOrderCreateModel()->getQuote()->removeAllItems();
			
			
            $this->_getOrderCreateModel()->getQuote()->setCustomerId($customerId);
            
            if( count($requestedItems) ){             
                //convert quote items to order
                Mage::app()->getStore()->setCurrentCurrencyCode($currencyCode);
                $this->_convertQuoteItemsToOrder($quoteadvId, $requestedItems);
                Mage::getSingleton('adminhtml/session')->setUpdateQuoteId($quoteadvId);
            }else{
                $msg = $this->__('To create an order, select product(s) and quantity');  
                Mage::getSingleton('adminhtml/session')->addError($msg ); 
                $url = $_SERVER['HTTP_REFERER']; 
                $this->_redirectUrl($url);
                return;
            }           
            
            
            
            $this->_getOrderCreateModel()
                ->initRuleData()
                ->saveQuote();
            $this->_getOrderCreateModel()->getSession()->setCurrencyId($currencyCode);   
            Mage::helper('qquoteadv')->sentAnonymousData('confirm', 'b');
            
            $url =  $this->getUrl('adminhtml/sales_order_create/index');
            $this->_redirectUrl($url);

            return;
        }else{
            $this->_redirect('*/*');
        }
    }   
    
    protected function _redirectErr($errorMsg){
        
        if(is_string($errorMsg)) $errorMsg = array($errorMsg);
        if( count($errorMsg) ){
            foreach($errorMsg as $msg){
                    Mage::getSingleton('adminhtml/session')->addError($msg);
            }

            $url = $_SERVER['HTTP_REFERER'];
            $this->_redirectUrl($url);				
        }
    }
    
     /**
     * Save customer
     *
     * @param $quote
     */
    protected function _saveCustomerAfterQuote($quote){         
                
        $customer           = $quote->getCustomer();
        $store              = $quote->getStore();
        $billingAddress     = null;
        $shippingAddress    = null; 
        if(!$quote->getCustomer()->getId() ) {
        	$customer->addData($quote->getBillingAddress()->exportCustomerAddress()->getData())
                    ->setPassword($customer->generatePassword())
                    ->setStore($store);
            $customer->setEmail($quote->getData('customer_email'));
            $customer->setGroupId($quote->getData('customer_group_id'));

            $customerBilling = $quote->getBillingAddress()->exportCustomerAddress();
            $customerBilling->setIsDefaultBilling(true);
            $customer->addAddress($customerBilling);

            $shipping = $quote->getShippingAddress();
            if (!$quote->isVirtual() && !$shipping->getSameAsBilling()) { 
               $customerShipping = $shipping->exportCustomerAddress();
               $firstname   = $customerShipping->getData('firstname');
               $lastname    = $customerShipping->getData('lastname');

              if( empty($firstname) || empty($lastname) ){
                $msg = $this->__("There was an error, because the customer shipping address was undefined");
                $this->_redirectErr( array($msg)); return;   
              }else{
                $customerShipping->setIsDefaultShipping(true);
                $customer->addAddress($customerShipping);
              }

            } else { 
               $customerBilling->setIsDefaultShipping(true);
            }
            
            try{
             $customer->save();
             $customer->sendNewAccountEmail('registered', '', $customer->getStoreId());  
            }catch(Exception $e){
             $this->_redirectErr( array($e->getMessage()) ); return;  
            }
        }
        
        // set customer to quote and convert customer data to quote
        $quote->setCustomer($customer);
    }
    
    public function switch2QuoteAction(){
        $this->swith2QuoteAction();
    }
   
    public function swith2QuoteAction() {
        
        //unique id for c2q session
	$c2qId	= Mage::getSingleton('adminhtml/session')->getUpdateQuoteId(); //null;
               
	//pool error messages
	$errorMsg	= array();     
        
        $quote	= Mage::getSingleton('adminhtml/session_quote')->getQuote();
        
        $baseToQuoteRate = $quote->getData('base_to_quote_rate');
        $currencyCode = $quote->getData('quote_currency_code');
        $customerId = $quote->getCustomer()->getId();
        if(!$customerId && $quote->getData('customer_email')){
            $this->_saveCustomerAfterQuote($quote);
            $customerId = $quote->getCustomer()->getId();
        }
        
        $billingAddress	= $quote->getBillingAddress();
        $shipAddress	= $quote->getShippingAddress();
         
        $email = $quote->getCustomer()->getEmail();       
        
        $items = $quote->getAllItems();
        
    	if (!Mage::getStoreConfig('qquoteadv/general/enabled', $quote->getStoreId())) {			
			$errorMsg[] = $this->__("Module is disabled");
		}		
        if (empty($customerId)) {
			$errorMsg[] = $this->__("Customer not recognized for new quote");
		}		
		if (empty($email) ) {
			$errorMsg[] = $this->__("Customer's email was undefined");
		}
		if (!count($items) ){
			$errorMsg[] = $this->__("There was an error, because the product quantities were not defined");
		}

                
                foreach($items as $item){

                    // TODO :
                    // Function doesn't work on all installations!
                    // Check if product is a configurable product
                    $checkConfigurable = Mage::helper('qquoteadv')->isConfigurable($item, $item->getData('qty'));
                    if( $checkConfigurable != false ) {                      
                        $qty = $checkConfigurable;                     
                    } else {
                        $qty = $item->getData('qty');
                    }
                  
                    $check = Mage::helper('qquoteadv')->isQuoteable($item, $qty);
                    
                    if($check->getHasErrors()){
                        $errors = $check->getErrors();
                        $errorMsg = array_merge($errorMsg, $errors);
                    }
                }
                
                Mage::unregister('conf_parent');

        //#return back in case any error found
        if( count($errorMsg) ){ $this->_redirectErr($errorMsg); return; }
        
        //#c2q insert data
        if($customerId && $email){ 
            $modelCustomer	= Mage::getModel('qquoteadv/qqadvcustomer');
	    
            
            $copyShippingParams = array(
                    'shipping_amount'=>'shipping_amount',
                    'base_shipping_amount'=>'base_shipping_amount',
                    'shipping_amount_incl_tax'=>'shipping_amount_incl_tax',
                    'base_shipping_amount_incl_tax'=>'base_shipping_amount_incl_tax',
                    'base_shipping_tax_amount'=>'base_shipping_tax_amount',
                    'shipping_tax_amount'=>'shipping_tax_amount',
                    'address_shipping_method'=>'shipping_method',
                    'address_shipping_description'=>'shipping_description',
                );
            
            
            
            
            $shipRates = $shipAddress->getShippingRatesCollection();
            
            $copyRateParams = array();
            $rate = null;
            foreach($shipRates as $rates){
                                
              if($rates['code'] == $shipAddress->getShippingMethod()){
               
               $rate = $rates;   
               $copyRateParams = array(
                    'shipping_method'=>'method', 
                    'shipping_description'=>'method_description',
                    'shipping_method_title'=>'method_title', 
                    'shipping_carrier'=>'carrier',
                    'shipping_carrier_title'=>'carrier_title',
                    'shipping_code'=>'code'
                );
                break;
              }
            }   
                     
            $shipStreet = "";
            $billStreet = "";
            $shipAddressExists = false;
            foreach($shipAddress->getStreet() as $addressLine) {
            	if($addressLine != "") $shipAddressExists = true;
            }
			
            $billAddressExists = false;
            foreach($billingAddress->getStreet() as $addressLine) {
            	if($addressLine != "") $shipAddressExists = true;
            }
			
			if($shipAddressExists)	$shipStreet = implode(',',$shipAddress->getStreet());
			if($billAddressExists)  $billStreet = implode(',',$billingAddress->getStreet());

            if(!$c2qId) {
                $name =  $billingAddress->getFirstname();
                if($name != "") { // &&  count($quote->getCustomer()->getAddresses()) ){
                    /* @var $helper Ophirah_Qquoteadv_Helper_Data */
                    $helper = Mage::helper('qquoteadv');
                    /* @var $admin Mage_Admin_Model_Session */
                    $admin = Mage::getSingleton('admin/session');
                    
                    $qcustomer = array(
                            'created_at' => NOW(),
                            'updated_at' => NOW(),
                          
                        
                            'customer_id' => $customerId,
                            'currency' => $currencyCode,
                            'base_to_quote_rate' => $baseToQuoteRate,
                            'prefix'        => $billingAddress->getPrefix(),
                            'firstname'     => $billingAddress->getFirstname(),
                            'middlename'    => $billingAddress->getMiddlename(),
                            'lastname'      => $billingAddress->getLastname(),
                            'suffix'        => $billingAddress->getSuffix(),                        
                            'company'       => $billingAddress->getCompany(),
                            'email'         => $email,
                            'country_id'    => $billingAddress->getCountryId(),
                            'region'        => $billingAddress->getRegion(),
                            'city'          => $billingAddress->getCity(),
                            'address'       => $billStreet,
                            'postcode'      => $billingAddress->getPostcode(),
                            'telephone'     => $billingAddress->getTelephone(),
                            'fax'           => $billingAddress->getFax(),
                            'store_id'      => $quote->getStoreId(),
                            'user_id'       => $helper->getExpectedQuoteAdminId($modelCustomer, $admin->getUserId()),
                            
                            //#shipping
                            'shipping_prefix'  => $shipAddress->getData("prefix"),
                            'shipping_firstname'     => $shipAddress->getData("firstname"),
                            'shipping_middlename'    => $shipAddress->getData("middlename"),
                            'shipping_lastname'      => $shipAddress->getData("lastname"),
                            'shipping_suffix'        => $shipAddress->getData("suffix"),                        
                            'shipping_company'       => $shipAddress->getData("company"),
                            'shipping_country_id'    => $shipAddress->getData("country_id"),
                            'shipping_region'        => $shipAddress->getData("region"),
                            'shipping_region_id'     => $shipAddress->getData("region_id"),
                            'shipping_city'          => $shipAddress->getData("city"),
                            'shipping_address'       => $shipStreet,
                            'shipping_postcode'      => $shipAddress->getData("postcode"),
                            'shipping_telephone'     => $shipAddress->getData("telephone"),
                            'shipping_fax'           => $shipAddress->getData("fax"),              
                        
                    );
                    
                    foreach($copyShippingParams  as $key){
                        $qcustomer[$key] = $shipAddress->getData($key);
                    }
                    
                    
                    foreach($copyRateParams as $key=>$value){
                        $qcustomer[$key] = $rate[$value];
                    }

                    //#add customer to c2q
                    try {
                        $c2qId = $modelCustomer->addQuote($qcustomer)->getQuoteId();

                        //#save c2q id into session
                        $this->getCustomerSession()->setQuoteadvId($c2qId);					
                    }catch(Exception $e){ Mage::log($e->getMessage()); }
                }else{
                    $errorMsg[] = $this->__("There was an error, because the customer address was undefined");
                }
            }else{ 
                $this->getCustomerSession()->setQuoteadvId($c2qId);
                $shipStreet = implode(',',$shipAddress->getStreet());
                $billingStreet = $shipStreet;
                $params = array();
                $params['currency'] = $currencyCode;
                $params['shipping_address'] = $shipStreet;
                //$params['shipping_title'] = $rates[$value];
                
                $billingParams = array('firstname', 'lastname', 'middlename', 'suffix', 'company', 'country_id', 'region_id', 'city');
                foreach($billingParams as $value){
                    //Mage::Log('CSP'.$key.":".$shipAddress->getData($value));
                    $params[$value] = $billingAddress->getData($value);
                }
                $params['street'] = $billingStreet;
                
                foreach($shipAddress->getData() as $key=>$value){
                   $params["shipping_" . $key] = $value;
                }
                
                foreach($copyShippingParams as $key=>$value){
                    //Mage::Log('CSP'.$key.":".$shipAddress->getData($value));
                   $params[$key] = $shipAddress->getData($value);
                }
                 
                foreach($copyRateParams as $key=>$value){
                   //  Mage::Log('CRP'.$key.":".$rate[$value]);
                   $params[$key] = $rate[$value];
                }
                
              
                if(count($params) > 0){
                    try{
                        $modelCustomer	= Mage::getModel('qquoteadv/qqadvcustomer')->updateQuote($c2qId, $params);
                    }catch(Exception $e){
                        Mage::log($e->getMessage());
                    }
                }
 
                
                
                $qCollection = Mage::getModel('qquoteadv/qqadvproduct');
                $ids = $qCollection->getIdsByQuoteId($c2qId);
                if($ids){
                    foreach($ids as $lineId){
                        try {
                           // remove item to quote mode
                           Mage::getModel('qquoteadv/qqadvproduct')->deleteQuote($lineId);
                         } catch (Exception $e) {
                                $errorQuote[] = $e->getMessage();
                         } 
                    }
                }
                
            }
            
            
            
            //#return back in case any error found
            if( count($errorMsg) ){ $this->_redirectErr($errorMsg); return; }

            //#parse in case quote has items
            foreach ($quote->getAllVisibleItems() as $item) { 
                $superAttribute = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct()); 

                $optionalAttrib = '';
                if (isset($superAttribute['info_buyRequest'])) {
                    if (isset($superAttribute['info_buyRequest']['uenc'])){
                        unset($superAttribute['info_buyRequest']['uenc']);
                    }
                    $superAttribute['info_buyRequest']['qty'] = $item->getQty();
                    $optionalAttrib = serialize($superAttribute['info_buyRequest']);
                    
                    
                }

                if($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                    $original_price = $item->getPrice();                    
                } else {
                    $original_price = $item->getProduct()->getPrice();
                }

                // Only Custom Prices needs to be recalculated by currency rate
                if($item->getOriginalCustomPrice()) {
                    $rate = $baseToQuoteRate;
                    $customPrice = $item->getOriginalCustomPrice();
                } else {    
                    $rate = 1;
                    $customPrice = $item->getPrice()*$baseToQuoteRate;
                }

                $params = array(
                        'product_id'        => $item->getProductId(),
                        'qty'               => $item->getQty(),
                        'price'             => $item->getPrice()/$rate,
                        'custom_price'      => $customPrice,
                        'original_price'    => $original_price,
                        'base_quote_rate'   => $baseToQuoteRate
                );
                
                $this->_create($params, $optionalAttrib);				
            }
            
            //#update c2q status to make visible c2q request 
            try{
                $modelCustomer->load($c2qId);
                $modelCustomer->setIsQuote(1);
                $modelCustomer->setStatus(Ophirah_Qquoteadv_Model_Status::STATUS_PROPOSAL_SAVED);
                
                //#for new quote we need correct increment id
                if(!Mage::getSingleton('adminhtml/session')->getUpdateQuoteId()){
                    $modelCustomer->setIncrementId(Mage::getModel('qquoteadv/entity_increment_numeric')->getNextId());                
                }
                
                // Save data
                $modelCustomer->save();
                
                 Mage::helper('qquoteadv')->sentAnonymousData('request','b');
            }catch(Exception $e){ Mage::log($e->getMessage()); }     
            
            Mage::getSingleton('adminhtml/session_quote')->clear();
            
		}//if
		
        Mage::getSingleton('adminhtml/session')->setUpdateQuoteId(null);
        
        if ($c2qId) {
          $this->_redirect('*/*/edit', array('id'=>$c2qId));
        }else{
           $this->_redirect('*/*');   
        }
    }    
    
    /**
     * Get customer session data
     */
    public function getCustomerSession() {
        return Mage::getSingleton('customer/session');
    }
     /**
     * Insert quote data 
      * $params = array(
		'product' => $item->getProductId(),
		'qty'     => $item->getQty(),
        'price'   => $item->getPrice()
        'original_price' => $item->getProduct()->getPrice();
		);
     * @param string $superAttribute
     */
    private function _create($params, $superAttribute) {
        $_product = Mage::getModel('catalog/product')->load($params['product_id']);
        
        $modelProduct	= Mage::getModel('qquoteadv/qqadvproduct');

        $customerId      = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getCustomer()->getId();
         
        $hasOption = 0;
        $options   = '';
        if (isset($params['options'])) {
               $options     = serialize($params['options']);
               $hasOption   = 1;
        } elseif(isset($superAttribute)) {
               $attr = unserialize($superAttribute);
               
               if (isset($attr['options'])) {
                $options        = serialize($attr['options']);
                $hasOption      = 1; 
                $params['qty']  = $attr['qty'];
               }
        }
        
        
        $quoteId = $this->getCustomerSession()->getQuoteadvId();
        $qproduct = array(
            'quote_id'      => $quoteId,
            'product_id'    => $params['product_id'],
            'qty'           => $params['qty'],
            'attribute'     => $superAttribute,
            'has_options'   => $hasOption,
            'options'       => $options,
            'store_id'		=> Mage::getSingleton('adminhtml/session_quote')->getStoreId()  //$this->getCustomerSession()->getStoreId()
        );

        
        // Get Currency rate from database
        $_quote = Mage::getModel('qquoteadv/qqadvcustomer')->load($quoteId);
        $rate = $params['base_quote_rate'];

        // Defining Prices       
        $basePrice      = $params['price'];
        $price          = $params['custom_price'];
        $orgPrice       = $params['original_price'];
        $orgCurPrice    = $orgPrice * $rate;       
        
        // OLD CODE - probably not used
//        if($price == $basePrice) {
//            // no custom price available so not converted to the currency
//            if(Mage::app()->getStore()->getCurrentCurrencyCode() != $_quote->getData('currency')){
//                $price = $price * $rate;
//            }
//        }       
      
        try{
            $obj = $modelProduct->addProduct($qproduct); 
            $requestData = array(
                    'quote_id'              => $this->getCustomerSession()->getQuoteadvId(),
                    'product_id'            => $params['product_id'],
                    'request_qty'           => $params['qty'],
                    'owner_base_price'      => $basePrice,
                    'owner_cur_price'       => $price,
                    'original_price'        => $orgPrice,
                    'original_cur_price'    => $orgCurPrice,
                    'quoteadv_product_id'   => $obj->getId()
            );

            Mage::getModel('qquoteadv/requestitem')->setData($requestData)->save();
	
        }catch(Exception $e) {
            Mage::log($e->getMessage()); 
        }                     
        
        return $this;
    }
    
    /**
     * Get core session data
    */
    public function getCoreSession() {
        return Mage::getSingleton('core/session');
    }

	public function deleteQtyFieldAction(){
		$requestId	= (int) $this->getRequest()->getParam('request_id');
		$c2qId		= null;
		if(empty($requestId)){ $this->_redirect('*/*/*'); }

		$item	= Mage::getModel('qquoteadv/requestitem')->load($requestId);
		$c2qId	= $item->getData('quote_id');
		
                 $_quote = Mage::getSingleton('qquoteadv/qqadvcustomer')->load($c2qId);
                
		$quoteProductId = $item->getData('quoteadv_product_id'); 
		$listRequests = Mage::getModel('qquoteadv/requestitem')->getCollection()->setQuote($_quote);
		$listRequests->addFieldToFilter('quoteadv_product_id', $quoteProductId);
		$size = $listRequests->getSize() ;

		if($size>1){ 
			try{
				$item->delete();
			}catch(Exception $e){ 
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());  
			}
		}else{
			$msg = $this->__('Minimum of one Qty is required');
			Mage::getSingleton('adminhtml/session')->addError($msg);
		}
		
		$this->_redirect('*/*/edit', array('id'=>$c2qId));
	}
    
	public function addQtyFieldAction() {
            $quoteProductId		= (int) $this->getRequest()->getParam('quote_product_id');
            $quoteProduct               = Mage::getModel('qquoteadv/qqadvproduct')->load($this->getRequest()->getParam('quote_product_id'));                       
            $product                    = Mage::getModel('catalog/product')->load($quoteProduct->getData('product_id'));
            
            // For configurable product, use the simple product 
            if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $attribute = $quoteProduct->getData('attribute');               
                if(!is_array($attribute)) {$attribute = unserialize($attribute);}
                $prod_simple = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($attribute['super_attribute'], $product);
                $check_prod = $prod_simple->getId();
                
            } else {
                $check_prod = $quoteProductId;
            }
            
            $requestQty			= (int) $this->getRequest()->getParam('request_qty');
            $c2qId                      = (int) $this->getRequest()->getParam('quoteadv_id');
            
            $check = Mage::helper('qquoteadv')->isQuoteable( $check_prod , $requestQty);
            if($check->getHasErrors()){
                $errors = $check->getErrors();
                $this->_redirectErr($errors);
                return;
            }

            $originalPrice	= 0;
            $productId		= null;

            if(empty($quoteProductId) or empty($requestQty)){
                $errorMsg = $this->__("Not valid data"); 
                Mage::getSingleton('adminhtml/session')->addError($errorMsg);
                
                if (!empty($c2qId)) {
                  return $this->_redirect('*/*/edit', array('id' => $c2qId));
                }
                else{
                  return $this->_redirect('*/*/');
                }
            }

            //#SEARCH ORIGINAL PRICE
             $_quote = Mage::getSingleton('qquoteadv/qqadvcustomer')->load($c2qId);
            
            $_collection = Mage::getModel('qquoteadv/requestitem')->getCollection()->setQuote($_quote)
                            ->addFieldToFilter('quoteadv_product_id', $quoteProductId);
       
            //#trying to find duplicate of requested quantity value
            foreach($_collection as $item){      
            $c2qId = $item->getData('quote_id');

            $productId = $item->getData('product_id');
            $check = Mage::helper('qquoteadv')->isQuoteable( $productId , $requestQty);
            if($check->getHasErrors()){
                $errors = $check->getErrors();
                $this->_redirectErr($errors);
                return;
            }
            
            if($requestQty == $item->getData('request_qty')){
				$errorMsg = $this->__('Duplicate value entered');
				Mage::getSingleton('adminhtml/session')->addError($errorMsg);
        			return $this->_redirect('*/*/edit', array('id'=>$c2qId));
			}
            
          	
		}
        
                $ownerPrice     = Mage::helper('qquoteadv')->_applyPrice($quoteProductId, $requestQty);  
                $originalPrice  = Mage::helper('qquoteadv')->_applyPrice($quoteProductId, 1);
                
                
                $_quoteadv = Mage::getModel('qquoteadv/qqadvcustomer')->load($c2qId);
                $baseCurrency = Mage::app()->getBaseCurrencyCode();
                $currencyCode = $_quoteadv->getData('currency');
                if($currencyCode == "") $currencyCode = $baseCurrency;
                if($currencyCode != $baseCurrency){
                      $rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrency,$currencyCode);
                      $rate = $rates[$currencyCode];
                }else{
                    $rate = 1;
                }
/*
 * EDIT - FIX : Table quoteadv_request_item
 * shows owner_base_price as custom price per piece
 */
//              $basePrice = $ownerPrice/$rate;
                $basePrice = $ownerPrice;


		if($c2qId && $productId && $originalPrice && $requestQty) {
			$requestData = array(
					'quote_id'              => $c2qId,
					'product_id'            => $productId,
					'request_qty'           => $requestQty,
					'owner_base_price'      => $basePrice,
                                        'owner_cur_price'       => $ownerPrice*$rate,
					'original_price'        => $originalPrice,
					'quoteadv_product_id'   => $quoteProductId,
                                        'original_cur_price'    => $basePrice*$rate                            
			);

			if($requestQty){
				try{
					Mage::getModel('qquoteadv/requestitem')->setData($requestData)->save();
				}catch(Exception $e){ 
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage()); 
				}
			}
		}
        if(!empty($c2qId)) {
          $this->_redirect('*/*/edit', array('id'=>$c2qId));
        }else{
          $this->_redirect('*/*/');
        }
	}
        
    /**
     * Acl check for admin
     *
     * @return bool
    */
    protected function _isAllowed()
    {   
	    $aclResource = 'admin/sales/qquoteadv';
		return Mage::getSingleton('admin/session')->isAllowed($aclResource);        
    }
    
    public function getAdminName($id){
		return Mage::helper('qquoteadv')->getAdminName($id);
    } 
    
    
    /**
    * Export selected quotes as csv
    */
    public function exportAction() {
        
        if(!Mage::helper('qquoteadv')->validLicense('export')){
          $this->_redirectErr($this->__('The CSV export function is only available in Cart2Quote Enterprise. 
          To update please go to <a href="http://www.cart2quote.com/pricing-magento-quotation-module.html?utm_source=Customer%2Bwebsite&utm_medium=license%2Bpopup&utm_campaign=Upgrade%2Bversion">http://www.cart2quote.com</a>')); 
          return;  
        }
       
        
    	$quoteIds = $this->getRequest()->getParam('qquote');
    
    	if(!is_array($quoteIds) || empty($quoteIds)) {
    		$this->_redirectErr($this->__('No quotes selected to export')); return;
    	}
    	$folder = Mage::getBaseDir().self::EXPORT_FOLDER_PATH;
    	$filename = "cart2quoteExport_".date("ymdHis").".csv";
    	
    	//check the folder exists or create it
    	if(!file_exists($folder)){
    		try{
    			mkdir($folder);
    		}catch(Exception $e){
    			Mage::Log($e->getMessage());
    			$this->_redirectErr($this->__('Could not create cart2quote export folder: '). $folder); return;
    		}
    	}else{
    		if(!is_writable($folder)){
    			$this->_redirectErr($this->__('The cart2quote export folder is not writable: '). $folder); return;
    		}
    	}
    	
    	//set filepath
    	$filepath = $folder.$filename;
    	
    	//export quotes to file
    	$exported = Mage::getSingleton('qquoteadv/qqadvcustomer')->exportQuotes($quoteIds, $filepath);  
    	
    	if($exported){
    		$contents = file_get_contents($filepath);
    		$this->_prepareDownloadResponse($filename, $contents);
    	}else{
    		$this->_redirectErr($this->__('Could not export quotes')); return;
    	}
    }
}
