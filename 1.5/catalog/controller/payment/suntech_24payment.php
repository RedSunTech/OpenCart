<?php
class ControllerPaymentSuntech24Payment extends Controller {
	protected function index() {
		$this->language->load('payment/suntech_24payment');

		$this->data['button_confirm'] = $this->language->get('button_confirm');

  		$this->data['action'] = 'https://www.esafe.com.tw/Service/Etopm.aspx';
//		$this->data['action'] = 'https://test.esafe.com.tw/Service/Etopm.aspx';

		$account = $this->config->get('suntech_24payment_account');
		$password = $this->config->get('suntech_24payment_password');
		$order_id = $this->session->data['order_id'];

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		$total_amount = intval($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
		$ChkValue = strtoupper(sha1($account.$password.$total_amount));

		$this->data['web'] = $account;
		$this->data['MN'] = $total_amount;
		$this->data['OrderInfo'] = '';
		$this->data['Td'] = $order_id;
		$this->data['sna'] = html_entity_decode($order_info['payment_lastname'] .' '. $order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$this->data['sdt'] = $order_info['telephone'];
		$this->data['email'] = $order_info['email'];
		$this->data['note1'] = $order_id;
		$this->data['note2'] = strtoupper(sha1($account.$password.$order_id.$total_amount)).',payment/suntech_24payment/callback';
		$duedate = mktime(0, 0, 0, date("m"), date("d")+intval($this->config->get('suntech_24payment_duedays')), date("Y"));
		$this->data['DueDate'] = date("Ymd",$duedate);
		$this->data['UserNo'] = '';
		$this->data['BillDate'] = date("Ymd");
		$this->data['ProductName1'] = $this->config->get('suntech_24payment_product_name');
		$this->data['ProductPrice1'] = $total_amount;
		$this->data['ProductQuantity1'] = '1';
		$this->data['ChkValue'] = $ChkValue;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/suntech_24payment.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/suntech_24payment.tpl';
		} else {
			$this->template = 'default/template/payment/suntech_24payment.tpl';
		}
		$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), $this->language->get('text_new_notify'), true);

		$this->render();
	}

	public function callback() {
	    $this->language->load('payment/suntech_24payment');
	    $data = $this->request->post;

		if (isset($data['note2'])) {
		    $isSuccess = true;

            //檢查訂單
            $order_id = $data['Td'];
            $this->load->model('checkout/order');
            $this->data['continue'] = $this->url->link('checkout/checkout', '', 'SSL');
			$this->data['heading_title'] = $this->language->get('heading_title_error');
			$this->data['title'] = $this->language->get('text_title');

            if ($isSuccess) {
			    $order_info = $this->model_checkout_order->getOrder($order_id);
			    
			    $account = $this->config->get('suntech_24payment_account');
			    $password = $this->config->get('suntech_24payment_password');
                
                //檢查送出之hash與金額
                $total_amount = intval($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
			    $output = strtoupper(sha1($account.$password.$order_id.$total_amount)).',payment/suntech_24payment/callback';
                if (($output!=urldecode($data['note2'])) || ($total_amount!=intval($data['MN']))) {
                    $isSuccess = false;

	        		$this->data['text_response'] = $this->language->get('text_response_authdenied');
			        $this->data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_authdenied'));
			        $this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
                }

			    //檢查ChkValue
			    if ($isSuccess) {
			        $output = strtoupper(sha1($account.$password.$data['buysafeno'].$total_amount.$data['EntityATM']));
                    if ($output!=$data['ChkValue']) {
                        $isSuccess = false;

				  		$this->data['text_response'] = $this->language->get('text_response_authdenied');
			            $this->data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_authdenied'));
			            $this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
                    }
                }
                
            }
            else {
			    $this->data['text_response'] = $this->language->get('text_response_timeout');
			    $this->data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_timeout'));
			    $this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
            }

			if ($isSuccess) {
				if ($data['EntityATM'] != '') {
				    $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
				    $message = sprintf($this->language->get('text_callback_notify'), $data['buysafeno'], $data['EntityATM']);
				    if (!$order_info['order_status_id']) {
				    	$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), $message, true);
				    } else {
				    	$this->model_checkout_order->update($order_id, $this->config->get('config_order_status_id'), $message, false);
				    }
			        $this->data['text_response'] = sprintf($this->language->get('text_response'),$data['EntityATM']);
			        $this->data['text_message'] = $this->language->get('text_callback');
			        $this->data['text_message_wait'] = sprintf($this->language->get('text_callback_wait'), $this->url->link('checkout/success'));
			        $this->data['continue'] = $this->url->link('checkout/success', '', 'SSL');
				} else {
			        $this->data['text_response'] = '';
			        $this->data['text_message'] = $this->language->get('text_failure');
			        $this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));
				}
			}
		}
        else {
			$this->data['text_response'] = $this->language->get('text_response_authdenied');
			$this->data['text_message'] = $this->language->get('text_failure');
			$this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
		}
		
		$this->data['charset'] = $this->language->get('charset');
		$this->data['language'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
	
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/suntech_result.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/suntech_result.tpl';
		} else {
			$this->template = 'default/template/payment/suntech_result.tpl';
		}

		$this->response->setOutput($this->render());
	}	 

	public function paid() {
		
	    $this->language->load('payment/suntech_24payment');
	    $data = $this->request->post;
		if (isset($data['note2'])) {
		    $isSuccess = true;

            //檢查訂單
            $order_id = $data['Td'];
            $this->load->model('checkout/order');
            $this->data['continue'] = $this->url->link('checkout/checkout', '', 'SSL');
			$this->data['heading_title'] = $this->language->get('heading_title_error');
			$this->data['title'] = $this->language->get('text_title');

            if ($isSuccess) {
			    $order_info = $this->model_checkout_order->getOrder($order_id);
			    
			    $account = $this->config->get('suntech_24payment_account');
			    $password = $this->config->get('suntech_24payment_password');
                
                //檢查送出之hash與金額
                $total_amount = intval($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
			    $output = strtoupper(sha1($account.$password.$order_id.$total_amount)).',payment/suntech_24payment/callback';
                if (($output!=$data['note2']) || ($total_amount!=intval($data['MN']))) {
                    $isSuccess = false;
			        $this->data['text_response'] = $this->language->get('text_response_authdenied');
			        $this->data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_authdenied'));
			        $this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
                }

			     //檢查ChkValue
			    if ($isSuccess) {
			        $output = strtoupper(sha1($account.$password.$data['buysafeno'].$total_amount.$data['errcode']));
                    if ($output!=$data['ChkValue']) {
                        $isSuccess = false;

				  		$this->data['text_response'] = $this->language->get('text_response_authdenied');
			            $this->data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_authdenied'));
			            $this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
                    }
                }
            }
            else {
			    $this->data['text_response'] = $this->language->get('text_response_timeout');
			    $this->data['text_message'] = sprintf($this->language->get('text_failure'),$this->language->get('text_response_timeout'));
			    $this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
            }

			if ($isSuccess) {
				if ($data['errcode'] == '00') {
				    $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
				    $message = sprintf($this->language->get('text_success_notify'), $data['buysafeno']);
				    
				    // 更新訂單狀態
				    if (!$order_info['order_status_id']) {
				    	$this->model_checkout_order->confirm($order_id, $this->config->get('suntech_24payment_order_status_id'), $message, true);
				    } else {
				    	$this->model_checkout_order->update($order_id, $this->config->get('suntech_24payment_order_status_id'), $message, true);
				    }

			        $this->data['text_response'] = $this->language->get('text_success_response');
			        $this->data['text_message'] = $this->language->get('text_success');
			        $this->data['text_message_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
			        $this->data['continue'] = $this->url->link('checkout/success', '', 'SSL');
				} else {
				    $message = sprintf($this->language->get('text_failure_notify').'', $data['buysafeno']);
			    	$this->model_checkout_order->update($order_id, $this->config->get('config_order_status_id'), $message, false);

			        $this->data['text_response'] = '';
			        $this->data['text_message'] = $this->language->get('text_failure');
			        $this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));
				}
			}
		}
        else {
			$this->data['text_response'] = $this->language->get('text_response_authdenied');
			$this->data['text_message'] = $this->language->get('text_failure');
			$this->data['text_message_wait'] = sprintf($this->language->get('text_failure_wait').'', $this->url->link('checkout/checkout', '', 'SSL'));
		}

		$this->data['charset'] = $this->language->get('charset');
		$this->data['language'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/suntech_empty.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/suntech_empty.tpl';
		} else {
			$this->template = 'default/template/payment/suntech_empty.tpl';
		}
		$this->response->setOutput($this->render());
	}	 

	function getHash($str) {
        return $str;
		return strtoupper(sha1($str));
	}

}
?>
