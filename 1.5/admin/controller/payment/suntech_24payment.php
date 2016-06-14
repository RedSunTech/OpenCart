<?php 
class ControllerPaymentSuntech24Payment extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/suntech_24payment');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('suntech_24payment', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		
		$this->data['entry_account'] = $this->language->get('entry_account');
		$this->data['entry_password'] = $this->language->get('entry_password');
		$this->data['entry_duedays'] = $this->language->get('entry_duedays');
		$this->data['entry_product_name'] = $this->language->get('entry_product_name');
		$this->data['entry_callback'] = $this->language->get('entry_callback');
		$this->data['entry_success'] = $this->language->get('entry_success');
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');
		
		$this->data['callback'] = HTTP_CATALOG . 'suntech_respond.php';
		$this->data['success'] = HTTP_CATALOG . 'index.php?route=payment/suntech_24payment/paid';
		//$this->data['callback'] = HTTP_CATALOG . 'index.php?route=payment/suntech_24payment/success';

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['account'])) {
			$this->data['error_account'] = $this->error['account'];
		} else {
			$this->data['error_account'] = '';
		}

 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

 		if (isset($this->error['duedays'])) {
			$this->data['error_duedays'] = $this->error['duedays'];
		} else {
			$this->data['error_duedays'] = '';
		}

 		if (isset($this->error['product_name'])) {
			$this->data['error_product_name'] = $this->error['product_name'];
		} else {
			$this->data['error_product_name'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/suntech_24payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/suntech_24payment', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['suntech_24payment_account'])) {
			$this->data['suntech_24payment_account'] = $this->request->post['suntech_24payment_account'];
		} else {
			$this->data['suntech_24payment_account'] = $this->config->get('suntech_24payment_account');
		}

		if (isset($this->request->post['suntech_24payment_password'])) {
			$this->data['suntech_24payment_password'] = $this->request->post['suntech_24payment_password'];
		} else {
			$this->data['suntech_24payment_password'] = $this->config->get('suntech_24payment_password');
		}

		if (isset($this->request->post['suntech_24payment_duedays'])) {
			$this->data['suntech_24payment_duedays'] = $this->request->post['suntech_24payment_duedays'];
		} else {
			$this->data['suntech_24payment_duedays'] = $this->config->get('suntech_24payment_duedays');
		}

		if (isset($this->request->post['suntech_24payment_product_name'])) {
			$this->data['suntech_24payment_product_name'] = $this->request->post['suntech_24payment_product_name'];
		} else {
			$this->data['suntech_24payment_product_name'] = $this->config->get('suntech_24payment_product_name');
		}

		if (isset($this->request->post['suntech_24payment_order_status_id'])) {
			$this->data['suntech_24payment_order_status_id'] = $this->request->post['suntech_24payment_order_status_id'];
		} else {
			$this->data['suntech_24payment_order_status_id'] = $this->config->get('suntech_24payment_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['suntech_24payment_geo_zone_id'])) {
			$this->data['suntech_24payment_geo_zone_id'] = $this->request->post['suntech_24payment_geo_zone_id'];
		} else {
			$this->data['suntech_24payment_geo_zone_id'] = $this->config->get('suntech_24payment_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['suntech_24payment_status'])) {
			$this->data['suntech_24payment_status'] = $this->request->post['suntech_24payment_status'];
		} else {
			$this->data['suntech_24payment_status'] = $this->config->get('suntech_24payment_status');
		}
		
		if (isset($this->request->post['suntech_24payment_sort_order'])) {
			$this->data['suntech_24payment_sort_order'] = $this->request->post['suntech_24payment_sort_order'];
		} else {
			$this->data['suntech_24payment_sort_order'] = $this->config->get('suntech_24payment_sort_order');
		}

		$this->template = 'payment/suntech_24payment.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/suntech_24payment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['suntech_24payment_account']) {
			$this->error['account'] = $this->language->get('error_account');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>