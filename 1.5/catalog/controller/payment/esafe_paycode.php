<?php

class ControllerPaymentEsafePaycode extends Controller {

    protected function index() {
        $this->language->load('payment/esafe_paycode');
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $itest_mode = $this->config->get('esafe_paycode_test_mode');
        $target = "_self";

        //判斷是否測試模式
        $url = ($itest_mode == '0' ? 'https://www.esafe.com.tw/Service/Etopm.aspx' : 'https://test.esafe.com.tw/Service/Etopm.aspx');

        $total_pay = intval(round($order_info['total']));
        //送出付款資訊
        $shtml = '<div style="text-align:center;" ><form name="myform" id="myform" method="post" target="' . $target . '" action="' . $url . '">';
        $shtml .="<input type='hidden' name='web' value='" . $this->config->get('esafe_paycode_storeid') . "' />"; //商店代號
        $shtml .="<input type='hidden' name='MN' value='" . $total_pay . "' />"; //交易金額
        $shtml .="<input type='hidden' name='Td' value='" . $order_info['order_id'] . "' />"; //商家訂單編號
        $shtml .="<input type='hidden' name='sna' value='" . $order_info['payment_lastname'] . $order_info['payment_firstname'] . "' />"; //消費者姓名
        if (preg_match("/^[0-9]+$/", $order_info["telephone"]) == 1) {
            $shtml .="<input type='hidden' name='sdt' value='" . $order_info["telephone"] . "' />"; //消費者電話
        }
        $shtml .="<input type='hidden' name='email' value='" . $order_info["email"] . "' />"; //消費者 Email

        $NewDate = Date('Ymd', strtotime("+" . $this->config->get('esafe_24pay_maxdays') . " days"));
        $shtml .="<input type='hidden' name='DueDate' value='" . $NewDate . "' />"; //繳費期限
        //$shtml .="<input type='hidden' name='UserNo' value='".$this->customer->getId()."' />"; //用戶編號
        //$shtml .="<input type='hidden' name='BillDate' value='".date('Ymd')."' />"; //列帳日期
        $shtml .="<input type='hidden' name='ChkValue' value='" . strtoupper(sha1($this->config->get('esafe_paycode_storeid') . $this->config->get('esafe_paycode_password') . $total_pay)) . "' />";
        //$shtml .= '<script type="text/javascript">document.myform.submit();</script>';
        $shtml .= '</form></div>';

        $this->data['shtml'] = $shtml;
        $this->data['total'] = $total_pay;

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['text_payment'] = $this->language->get('text_payment');
        $this->data['text_instruction'] = $this->language->get('text_instruction');
        $this->data['text_total_error'] = $this->language->get('text_total_error');
        $this->data['esafe_paycode_description'] = nl2br($this->config->get('esafe_paycode_description_' . $this->config->get('config_language_id')));

        $this->data['continue'] = $this->url->link('payment/esafe_paycode/confirm', '', '');
        
        $this->session->data['payment_duedate'] = $NewDate;

        if (isset($this->session->data['doubleclick'])) {
            unset($this->session->data['doubleclick']);
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/esafe_paycode.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/esafe_paycode.tpl';
        } else {
            $this->template = 'default/template/payment/esafe_paycode.tpl';
        }
        $this->render();
    }

    public function confirm() {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']); //取得訂單資訊
        //$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('esafe_paycode_order_status_id'), $order_info['comment']);
        //$this->cart->clear();
    }

    //即時回傳
    public function result() {
        $this->language->load('payment/esafe_paycode');
        if ($_POST["ChkValue"] == strtoupper(sha1($_POST["web"] . $this->config->get('esafe_paycode_password') . $_POST['buysafeno'] . $_POST['MN'] . $_POST['paycode']))) {

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($_POST['Td']); //取得訂單資訊
            $status = $this->config->get('esafe_paycode_order_status_id');
            //$finish_status = $this->config->get('esafe_paycode_order_finish_status_id');

            if ($_POST["SendType"] == 2) {
                // 商家用訊息
                $comment = $this->language->get('text_payment_no') . urldecode($_POST["buysafeno"]);
                $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $order_info['order_id'] . "', '" . $status . "', '0', '" . $comment . "', NOW());";
                $this->db->query($strSQL);

                //繳費期限
                $NewDate = '';
                if ($this->session->data['payment_duedate']!='') {
                    $NewDate = ' ' . substr($this->session->data['payment_duedate'],0,4) . '/' . substr($this->session->data['payment_duedate'],4,2) . '/' . substr($this->session->data['payment_duedate'],6,2) . ' ';
                    unset($this->session->data['payment_duedate']);
                }
                //4：全家超商、5：統一超商、6：OK 超商、7：萊爾富超商
                $strPayType = "";
                $arrPayType = explode(',', $_POST["PayType"]);
                if (in_array(4, $arrPayType)) {
                    $strPayType .= $this->language->get('text_paytype4');
                }
                if (in_array(5, $arrPayType)) {
                    if ($strPayType!='') {$strPayType .= $this->language->get('text_separate');}
                    $strPayType .= $this->language->get('text_paytype5');
                }
                if (in_array(6, $arrPayType)) {
                    if ($strPayType!='') {$strPayType .= $this->language->get('text_separate');}
                    $strPayType .= $this->language->get('text_paytype6');
                }
                if (in_array(7, $arrPayType)) {
                    if ($strPayType!='') {$strPayType .= $this->language->get('text_separate');}
                    $strPayType .= $this->language->get('text_paytype7');
                }
                $comment = sprintf($this->language->get('text_order_received'),$NewDate,$strPayType,urldecode($_POST["paycode"]));

                // 新增訂單通知處理歷程。
                if (!$order_info['order_status_id']) {
                    $this->model_checkout_order->confirm($order_info['order_id'], $status, $comment, true);
                }
                else {
                    $this->model_checkout_order->update($order_info['order_id'], $status, $comment, false);
                }

                // 更新訂單狀態。
                //$strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $status . "', date_modified = NOW() WHERE order_id = '" . $order_info['order_id'] . "';";
                //$this->db->query($strSQL);
            }
            $this->redirect($this->url->link('checkout/success'));
        }
    }

    //背景回傳
    public function callback() {
        $this->language->load('payment/esafe_paycode');
        if ($_POST["ChkValue"] == strtoupper(sha1($_POST["web"] . $this->config->get('esafe_paycode_password') . $_POST['buysafeno'] . $_POST['MN'] . $_POST['errcode']))) {

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($_POST['Td']); //取得訂單資訊
            $status = $this->config->get('esafe_paycode_order_status_id');
            $finish_status = $this->config->get('esafe_paycode_order_finish_status_id');

            //檢查web,MN是否正確
            if (($_POST["web"]!=$this->config->get('esafe_paycode_storeid')) || (intval($_POST["MN"])!=intval(round($order_info['total']))) || (intval($_POST["MN"])==0)) {
                echo '9999';
                exit();
            }

            if (!isset($_POST["SendType"]) || ($_POST["SendType"] == 1)) {
                if ($_POST["errcode"] == '00') {
                    $comment = $this->language->get('text_success_notify').$_POST["buysafeno"];
                    if ($_POST['PayDate']!='') {
                        $comment .= $this->language->get('text_separate') . $this->language->get('text_paydate') . substr($_POST['PayDate'],0,4) . '/' . substr($_POST['PayDate'],4,2) . '/' . substr($_POST['PayDate'],6,2);
                    }
                    if ($_POST['PayType']!='') {
                        $comment .= $this->language->get('text_separate') . $this->language->get('text_payment_name') . $this->language->get('text_paytype'.$_POST['PayType']);
                    }

                    // 新增訂單通知處理歷程。
                    $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $order_info['order_id'] . "', '" . $finish_status . "', '1', '" . $comment . "', NOW());";
                    $this->db->query($strSQL);

                    // 更新訂單狀態。
                    $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $finish_status . "', date_modified = NOW() WHERE order_id = '" . $order_info['order_id'] . "';";
                    $this->db->query($strSQL);
                } else {
                    $comment = $this->language->get('text_errmsg') . urldecode($_POST['errmsg']) . (($_POST['errcode']!='') ? sprintf($this->language->get('text_failure_reason_code'),$_POST['errcode']):'');
                    $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $order_info['order_id'] . "', '" . $status . "', '1', '" . $comment . "', NOW());";
                    $this->db->query($strSQL);
                }
                echo '0000';
                exit();
            }
        }
    }

}
?>

