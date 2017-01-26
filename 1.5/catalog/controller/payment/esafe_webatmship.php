<?php

class ControllerPaymentEsafeWebATMShip extends Controller {

    protected function index() {
        $this->language->load('payment/esafe_webatmship');
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $itest_mode = $this->config->get('esafe_webatmship_test_mode');
        $Term = ""; //分期期數
        $target = "_self";

        //判斷是否測試模式
        $url = ($itest_mode == '0' ? 'https://www.esafe.com.tw/Service/Etopm.aspx' : 'https://test.esafe.com.tw/Service/Etopm.aspx');

        $total_pay = intval(round($order_info['total']));
        //送出付款資訊
        $shtml = '<div style="text-align:center;" ><form name="myform" id="myform" method="post" target="' . $target . '" action="' . $url . '">';
        $shtml .="<input type='hidden' name='web' value='" . $this->config->get('esafe_webatm_storeid') . "' />"; //商店代號
        $shtml .="<input type='hidden' name='MN' value='" . $total_pay . "' />"; //交易金額
        $shtml .="<input type='hidden' name='Td' value='" . $order_info['order_id'] . "' />"; //商家訂單編號
        $shtml .="<input type='hidden' name='sna' value='" . $order_info['payment_lastname'] . $order_info['payment_firstname'] . "' />"; //消費者姓名
        if (preg_match("/^[0-9]+$/", $order_info["telephone"]) == 1) {
            $shtml .="<input type='hidden' name='sdt' value='" . $order_info["telephone"] . "' />"; //消費者電話
        }
        $shtml .="<input type='hidden' name='email' value='" . $order_info["email"] . "' />"; //消費者 Email
        $shtml .="<input type='hidden' name='CargoFlag' value='1' />";

        $shtml .="<input type='hidden' name='note1' value='webatmship' />";
        $shtml .="<input type='hidden' name='ChkValue' value='" . strtoupper(sha1($this->config->get('esafe_webatm_storeid') . $this->config->get('esafe_webatm_password') . $total_pay)) . "' />";
//$shtml .= '<script type="text/javascript">document.myform.submit();</script>';
        $shtml .= '</form></div>';

        $this->data['shtml'] = $shtml;
        $this->data['total'] = $total_pay;

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');
        $this->data['text_payment'] = $this->language->get('text_payment');
        $this->data['text_instruction'] = $this->language->get('text_instruction');
        $this->data['text_total_error'] = $this->language->get('text_total_error');
        $this->data['esafe_webatmship_description'] = nl2br($this->config->get('esafe_webatmship_description_' . $this->config->get('config_language_id')));

        $this->data['continue'] = $this->url->link('payment/esafe_webatmship/confirm', '', '');

        if (isset($this->session->data['doubleclick'])) {
            unset($this->session->data['doubleclick']);
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/esafe_webatmship.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/esafe_webatmship.tpl';
        } else {
            $this->template = 'default/template/payment/esafe_webatmship.tpl';
        }
        $this->render();
    }

    public function confirm() {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']); //取得訂單資訊
        //$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('esafe_webatmship_order_status_id'), $order_info['comment']);
        //$this->cart->clear();
    }

    //成功交易
    public function result() {
        $this->language->load('payment/esafe_webatmship');
        if ($_POST["note1"] == 'webatmship') { //超商取貨（WebATM付款）
            if ($_POST["SendType"] == 2) {
                if ($_POST["ChkValue"] == strtoupper(sha1($_POST["web"] . $this->config->get('esafe_webatmship_password') . $_POST['buysafeno'] . $_POST['MN'] . $_POST['errcode'] . $_POST["CargoNo"]))) {

                    $this->load->model('checkout/order');
                    $order_info = $this->model_checkout_order->getOrder($_POST['Td']); //取得訂單資訊
                    $status = $this->config->get('esafe_webatmship_order_status_id');
                    $finish_status = $this->config->get('esafe_webatmship_order_paid_status_id');

                    if ($_POST["errcode"] == '00') {
                        $comment = sprintf($this->language->get('text_success_notify'),urldecode($_POST["buysafeno"]));
                        if (!$order_info['order_status_id']) {
                            $this->model_checkout_order->confirm($_POST['Td'], $finish_status, $comment, true);
                        }
                        else {
                            $this->model_checkout_order->update($_POST['Td'], $finish_status, $comment, false);
                        }
                        if ($_POST["CargoNo"]!='') {
                            $comment = $this->language->get('text_cargono').urldecode($_POST["CargoNo"]).sprintf($this->language->get('text_cargono_query'),$_POST["CargoNo"]);
                            $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $finish_status . "', '1', '" . $comment . "', NOW());";
                            $this->db->query($strSQL);
                        }

                        $this->redirect($this->url->link('checkout/success'));
                    } else {
                        $comment = ($_POST["errcode"]!='') ? urldecode($_POST["errmsg"]).sprintf($this->language->get('text_failure_reason_code'),$_POST["errcode"]):$this->language->get('text_interrupt');
                        if (!$order_info['order_status_id']) {
                            $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $status . "', '1', '" . $this->language->get('text_failure_notify').$comment . "', NOW());";
                            $this->db->query($strSQL);
                            $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $status . "', date_modified = NOW() WHERE order_id = '" . $_POST['Td'] . "';";
                            $this->db->query($strSQL);
                        }
                        $this->session->data['payment_errmsg'] = $comment;
                        $this->redirect($this->url->link('checkout/esafefailure'));
                    }
                }
            }
        } else { //WebATM付款
            if ($_POST["SendType"] == 2) {
                if ($_POST["ChkValue"] == strtoupper(sha1($_POST["web"] . $this->config->get('esafe_webatm_password') . $_POST['buysafeno'] . $_POST['MN'] . $_POST['errcode']))) {

                    $this->load->model('checkout/order');
                    $order_info = $this->model_checkout_order->getOrder($_POST['Td']); //取得訂單資訊
                    $status = $this->config->get('esafe_webatm_order_status_id');
                    $finish_status = $this->config->get('esafe_webatm_order_finish_status_id');

                    if ($_POST["errcode"] == '00') {
                        $comment = sprintf($this->language->get('text_success_notify'),urldecode($_POST["buysafeno"]));

                        if (!$order_info['order_status_id']) {
                            $this->model_checkout_order->confirm($_POST['Td'], $finish_status, $comment, true);
                        }
                        else {
                            $this->model_checkout_order->update($_POST['Td'], $finish_status, $comment, false);
                        }
                        $this->redirect($this->url->link('checkout/success'));
                    } else {
                        $comment = ($_POST["errcode"]!='') ? urldecode($_POST["errmsg"]).sprintf($this->language->get('text_failure_reason_code'),$_POST["errcode"]):$this->language->get('text_interrupt');
                        if (!$order_info['order_status_id']) {
                            $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $status . "', '1', '" . $this->language->get('text_failure_notify').$comment . "', NOW());";
                            $this->db->query($strSQL);
                            $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $status . "', date_modified = NOW() WHERE order_id = '" . $_POST['Td'] . "';";
                            $this->db->query($strSQL);
                        }
                        $this->session->data['payment_errmsg'] = $comment;
                        $this->redirect($this->url->link('checkout/esafefailure'));
                    }
                }
            }
        }
    }

    //失敗交易/物流狀態
    public function callback() {
        $this->language->load('payment/esafe_webatmship');
        if ($_POST["note1"] == 'webatmship') { //超商取貨（WebATM付款）
            if ($_POST["SendType"] == 1) { //背景回傳：接收物流狀態
                if ($_POST["ChkValue"] == strtoupper(sha1($_POST["web"] . $this->config->get('esafe_webatmship_password') . $_POST['buysafeno'] . $_POST['StoreType']))) {

                    $this->load->model('checkout/order');
                    $order_info = $this->model_checkout_order->getOrder($_POST['Td']); //取得訂單資訊
                    // $status = $this->config->get('esafe_webatmship_order_status_id');
                    if ($_POST["StoreType"] == '1010') { //用戶已領
                        $finish_status = $this->config->get('esafe_webatmship_order_finish_status_id');
                    }
                    elseif ($_POST["StoreType"] == '101') { //抵達門市
                        $finish_status = $this->config->get('esafe_webatmship_order_shipped_status_id');
                    }
                    else {
                        $finish_status = $this->config->get('esafe_webatmship_order_paid_status_id');
                    }

                    $comment = $this->language->get('text_shippingmsg').urldecode($_POST["StoreMsg"]).(($_POST["StoreType"]!='') ? sprintf($this->language->get('text_failure_reason_code'),$_POST["StoreType"]):'');
                    $this->model_checkout_order->update($_POST['Td'], $finish_status, $comment, true);

                    // 新增訂單通知處理歷程。
                    // $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $finish_status . "', '1', '" . $comment . "', NOW());";
                    // $this->db->query($strSQL);
                    echo '0000';
                    exit();
                }
            } elseif ($_POST["SendType"] == 2) { //網頁回傳：付款失敗
                if ($_POST["ChkValue"] == strtoupper(sha1($_POST["web"] . $this->config->get('esafe_webatmship_password') . $_POST['buysafeno'] . $_POST['MN'] . $_POST['errcode'] . $_POST["CargoNo"]))) {

                    $this->load->model('checkout/order');
                    $order_info = $this->model_checkout_order->getOrder($_POST['Td']); //取得訂單資訊
                    $status = $this->config->get('esafe_webatmship_order_status_id');
                    $finish_status = $this->config->get('esafe_webatmship_order_finish_status_id');

                    $comment = ($_POST["errcode"]!='') ? urldecode($_POST["errmsg"]).sprintf($this->language->get('text_failure_reason_code'),$_POST["errcode"]):$this->language->get('text_interrupt');

                    if ($_POST["errcode"] == '00') {
                        // 新增訂單通知處理歷程。
                        $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $finish_status . "', '1', '" . $comment . "', NOW());";
                        $this->db->query($strSQL);

                        // 更新訂單狀態。
                        $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $finish_status . "', date_modified = NOW() WHERE order_id = '" . $_POST['Td'] . "';";
                        $this->db->query($strSQL);
                        $this->redirect($this->url->link('checkout/success'));
                    } else {
                        if (!$order_info['order_status_id']) {
                            $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $status . "', '1', '" . $this->language->get('text_failure_notify').$comment . "', NOW());";
                            $this->db->query($strSQL);
                            $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $status . "', date_modified = NOW() WHERE order_id = '" . $_POST['Td'] . "';";
                            $this->db->query($strSQL);
                        }
                        $this->session->data['payment_errmsg'] = $comment;
                        $this->redirect($this->url->link('checkout/esafefailure'));
                    }
                }
            }
        } else { //WebATM付款
            if ($_POST["ChkValue"] == strtoupper(sha1($_POST["web"] . $this->config->get('esafe_webatm_password') . $_POST['buysafeno'] . $_POST['MN'] . $_POST['errcode']))) {
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($_POST['Td']); //取得訂單資訊
                $status = $this->config->get('esafe_webatm_order_status_id');
                $finish_status = $this->config->get('esafe_webatm_order_finish_status_id');
                $comment = ($_POST["errcode"]!='') ? urldecode($_POST["errmsg"]).sprintf($this->language->get('text_failure_reason_code'),$_POST["errcode"]):$this->language->get('text_interrupt');

                if ($_POST["SendType"] == 1) { //背景回傳
                    if ($_POST["errcode"] == '00') { //此情況不會發生
//                        // 新增訂單通知處理歷程。
//                        $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $finish_status . "', '1', '" . $comment . "', NOW());";
//                        $this->db->query($strSQL);
//
//                        // 更新訂單狀態。
//                        $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $finish_status . "', date_modified = NOW() WHERE order_id = '" . $_POST['Td'] . "';";
//                        $this->db->query($strSQL);
                    } else {
                        if (!$order_info['order_status_id']) {
                            $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $status . "', '1', '" . $this->language->get('text_failure_notify').$comment . "', NOW());";
                            $this->db->query($strSQL);
                            $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $status . "', date_modified = NOW() WHERE order_id = '" . $_POST['Td'] . "';";
                            $this->db->query($strSQL);
                        }
                    }
                    echo '0000';
                    exit();
                } else { //網頁回傳
                    if ($_POST["errcode"] == '00') { //此情況不會發生
//                        // 新增訂單通知處理歷程。
//                        $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $finish_status . "', '1', '" . $comment . "', NOW());";
//                        $this->db->query($strSQL);
//
//                        // 更新訂單狀態。
//                        $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $finish_status . "', date_modified = NOW() WHERE order_id = '" . $_POST['Td'] . "';";
//                        $this->db->query($strSQL);
//                        $this->redirect($this->url->link('checkout/success'));
                    } else {
                        if (!$order_info['order_status_id']) {
                            $strSQL = "INSERT INTO " . DB_PREFIX . "order_history (order_id, order_status_id, notify, comment, date_added) values ('" . $_POST['Td'] . "', '" . $status . "', '1', '" . $this->language->get('text_failure_notify').$comment . "', NOW());";
                            $this->db->query($strSQL);
                            $strSQL = "UPDATE " . DB_PREFIX . "order SET order_status_id = '" . $status . "', date_modified = NOW() WHERE order_id = '" . $_POST['Td'] . "';";
                            $this->db->query($strSQL);
                        }
                        $this->session->data['payment_errmsg'] = $comment;
                        $this->redirect($this->url->link('checkout/esafefailure'));
                    }
                }
            }
        }
    }

}
?>

