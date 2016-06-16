<?php
class ControllerPaymentSuntechPaycode extends Controller {

	// 訂單送出前 , 清空購物車 , 建立訂單
	public function saveOrder() {
		// Load language 
		$this->language->load('payment/suntech_paycode');
		// Load Order Model
		$this->load->model('checkout/order');
		// Add Order History
        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1, $this->language->get('text_order_checkout'), true);
        // 清空購物車
		if ($this->cart->hasProducts()) {
    		$this->cart->clear();
		}
	}

	public function index() {
		$this->language->load('payment/suntech_paycode');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['action'] = 'https://www.esafe.com.tw/Service/Etopm.aspx';
		// for test server
		//$data['action'] = 'https://test.esafe.com.tw/Service/Etopm.aspx';

		$account = $this->config->get('suntech_paycode_account');
		$password = $this->config->get('suntech_paycode_password');
		$order_id = $this->session->data['order_id'];

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		$total_amount = intval($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
		$ChkValue = strtoupper(sha1($account.$password.$total_amount));

		$data['web'] = $account;
		$data['MN'] = $total_amount;
		$data['OrderInfo'] = '';
		$data['Td'] = $order_id;
		$data['sna'] = html_entity_decode($order_info['payment_lastname'] .' '. $order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$data['sdt'] = $order_info['telephone'];
		$data['email'] = $order_info['email'];
		$data['note1'] = $order_id;
		$data['note2'] = strtoupper(sha1($account.$password.$order_id.$total_amount)).',payment/suntech_paycode/callback';
		$duedate = mktime(0, 0, 0, date("m"), date("d")+intval($this->config->get('suntech_paycode_duedays')), date("Y"));
		$data['DueDate'] = date("Ymd",$duedate);
		$data['BillDate'] = date("Ymd");
		$data['UserNo'] = '';
		$data['ChkValue'] = $ChkValue;

		# Get the template
		$config_template = $this->config->get('config_template');
		$payment_template = '';
		if (file_exists(DIR_TEMPLATE . $config_template)) {
			$payment_template = $config_template;
		} else {
			$payment_template = 'default';
		}
		$payment_template .= (strpos(VERSION, '2.2.') !== false) ? '/payment/suntech_paycode.tpl' : '/template/payment/suntech_paycode.tpl';

		
		return $this->load->view($payment_template, $data);

	}

	// 繳款成功接收網址 or 交易完成接收網址
	public function callback() {

	    $this->language->load('payment/suntech_paycode');
	    $data = $this->request->post;
		if (isset($data['note2'])) {
		    $isSuccess = true;

            //檢查訂單
            $order_id = $data['Td'];
            $this->load->model('checkout/order');
            $data['continue'] = $this->url->link('checkout/checkout', '', 'SSL');
			$data['heading_title'] = $this->language->get('heading_title_error');
			$data['title'] = $this->language->get('text_title');

            if ($isSuccess) {
			    $order_info = $this->model_checkout_order->getOrder($order_id);
			    
			    $account = $this->config->get('suntech_paycode_account');
			    $password = $this->config->get('suntech_paycode_password');
                
                //檢查送出之hash與金額
                $total_amount = intval($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
			    $output = strtoupper(sha1($account.$password.$order_id.$total_amount)).',payment/suntech_paycode/callback';
                if (($output!=urldecode($data['note2'])) || ($total_amount!=intval($data['MN']))) {
                    $isSuccess = false;
			        $data['text_response'] = $this->language->get('text_response_authdenied');
			        $data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_authdenied'));
			        $data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
                }

			    //檢查ChkValue
			    if ($isSuccess) {
			        $output = strtoupper(sha1($account.$password.$data['buysafeno'].$total_amount.$data['paycode']));
                    if ($output!=$data['ChkValue']) {
                        $isSuccess = false;
			            $data['text_response'] = $this->language->get('text_response_authdenied');
			            $data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_authdenied'));
			            $data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
                    }
                }
            }
            else {
			    $data['text_response'] = $this->language->get('text_response_timeout');
			    $data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_timeout'));
			    $data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
            }

			if ($isSuccess) {
				if ($data['paycode'] != '') {
				    $data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
				    $message = sprintf($this->language->get('text_callback_notify'), $data['buysafeno'], $data['paycode']);
				    if (!$order_info['order_status_id']) {
				    	$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'), $message, true);
				    } else {
				    	$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'), $message, false);
				    }
				    
			        $data['text_response'] = sprintf($this->language->get('text_response'),$data['paycode']);
			        $data['text_message'] = $this->language->get('text_callback');
			        $data['text_message_wait'] = sprintf($this->language->get('text_callback_wait'), $this->url->link('checkout/success'));
			        $data['continue'] = $this->url->link('checkout/success', '', 'SSL');
				} else {
			        $data['text_response'] = '';
			        $data['text_message'] = $this->language->get('text_failure');
			        $data['text_message_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));
				}
			}
		}
        else {
			$data['text_response'] = $this->language->get('text_response_authdenied');
			$data['text_message'] = $this->language->get('text_failure');
			$data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
		}

		$data['charset'] = $this->language->get('charset');
		$data['language'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');


			// 整合首頁的樣板
		 $this->document->setTitle($this->config->get('config_meta_title'));
         $this->document->setDescription($this->config->get('config_meta_description'));
         $this->document->setKeywords($this->config->get('config_meta_keyword'));

                if (isset($this->request->get['route'])) {
                        $this->document->addLink(HTTP_SERVER, 'canonical');
                }

                $data['column_left'] = $this->load->controller('common/column_left');
                $data['column_right'] = $this->load->controller('common/column_right');
                $data['content_top'] = $this->load->controller('common/content_top');
                $data['content_bottom'] = $this->load->controller('common/content_bottom');
                $data['footer'] = $this->load->controller('common/footer');
                $data['header'] = $this->load->controller('common/header');

                $payment_template .= (strpos(VERSION, '2.2.') !== false) ? '/payment/suntech_result.tpl' : '/template/payment/suntech_result.tpl';

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . $payment_template)) {
                        $this->response->setOutput($this->load->view($this->config->get('config_template') . $payment_template, $data));
                } else {
                        $this->response->setOutput($this->load->view('default/template/payment/suntech_result.tpl', $data));
                }
		

	}	 
	
	// 交易回傳確認網址
	public function confirm() {
	    $this->language->load('payment/suntech_paycode');
	    $data = $this->request->post;
		if (isset($data['note2'])) {
		    $isSuccess = true;

            //檢查訂單
            $order_id = $data['Td'];
            $this->load->model('checkout/order');
            $data['continue'] = $this->url->link('checkout/checkout', '', 'SSL');
			$data['heading_title'] = $this->language->get('heading_title_error');
			$data['title'] = $this->language->get('text_title');

            if ($isSuccess) {
			    $order_info = $this->model_checkout_order->getOrder($order_id);
			    
			    $account = $this->config->get('suntech_paycode_account');
			    $password = $this->config->get('suntech_paycode_password');
                
                //檢查送出之hash與金額
                $total_amount = intval($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
			    $output = strtoupper(sha1($account.$password.$order_id.$total_amount)).',payment/suntech_paycode/callback';
                if (($output!=$data['note2']) || ($total_amount!=intval($data['MN']))) {
                    $isSuccess = false;
			        $data['text_response'] = $this->language->get('text_response_authdenied');
			        $data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_authdenied'));
			        $data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
                }

			    //檢查ChkValue
			    if ($isSuccess) {
			        $output = strtoupper(sha1($account.$password.$data['buysafeno'].$total_amount.$data['errcode']));
                    if ($output!=$data['ChkValue']) {
                        $isSuccess = false;
			            $data['text_response'] = $this->language->get('text_response_authdenied');
			            $data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_authdenied'));
			            $data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
                    }
                }
            }
            else {
			    $data['text_response'] = $this->language->get('text_response_timeout');
			    $data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_timeout'));
			    $data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
            }

			if ($isSuccess) {
				if ($data['errcode'] == '00') {
				    $data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
				    $message = sprintf($this->language->get('text_success_notify'), $data['buysafeno']);
				    
				    // 更新訂單狀態
				    if (!$order_info['order_status_id']) {
				    	$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('suntech_paycode_order_status_id'), $message, true);
				    } else {
				    	$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('suntech_paycode_order_status_id'), $message, true);
				    }

			        $data['text_response'] = $this->language->get('text_success_response');
			        $data['text_message'] = $this->language->get('text_success');
			        $data['text_message_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
			        $data['continue'] = $this->url->link('checkout/success', '', 'SSL');
				} else {
				    $message = sprintf($this->language->get('text_failure_notify').'', $data['buysafeno']);
			    	$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'), $message, false);

			        $data['text_response'] = '';
			        $data['text_message'] = $this->language->get('text_failure');
			        $data['text_message_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));
				}
			}
		}
        else {
			$data['text_response'] = $this->language->get('text_response_authdenied');
			$data['text_message'] = $this->language->get('text_failure');
			$data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
		}

		$data['charset'] = $this->language->get('charset');
		$data['language'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		# Get the template
		$config_template = $this->config->get('config_template');
		$payment_template = '';
		if (file_exists(DIR_TEMPLATE . $config_template)) {
			$payment_template = $config_template;
		} else {
			$payment_template = 'default';
		}
		$payment_template .= (strpos(VERSION, '2.2.') !== false) ? '/payment/suntech_empty.tpl' : '/template/payment/suntech_empty.tpl';

		return $this->response->setOutput($this->load->view($payment_template, $data));

	

	}	 

}
?>