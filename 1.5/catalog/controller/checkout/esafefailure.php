<?php

class ControllerCheckoutEsafeFailure extends Controller {

    public function index() {
        $this->load->language('checkout/esafefailure');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart'),
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/checkout', '', 'SSL'),
            'text' => $this->language->get('text_checkout'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/success'),
            'text' => $this->language->get('text_success'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_message'] = sprintf($this->language->get('text_message'), $this->session->data['payment_errmsg']);
        if (isset($this->session->data['payment_errmsg'])) {unset($this->session->data['payment_errmsg']);}

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['continue'] = $this->url->link('checkout/checkout');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/esafefailure.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/esafefailure.tpl';
        } else {
            $this->template = 'default/template/checkout/esafefailure.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

}
