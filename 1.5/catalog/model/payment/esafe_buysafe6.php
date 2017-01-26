<?php

class ModelPaymentEsafeBuysafe6 extends Model {

    public function getMethod($address, $total) {
        $this->load->language('payment/esafe_buysafe6');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('esafe_buysafe6_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('esafe_buysafe6_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('esafe_buysafe6_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }
        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'esafe_buysafe6',
                'title' => $this->language->get('text_title'),
                'sort_order' => $this->config->get('esafe_buysafe6_sort_order')
            );
        }
        return $method_data;
    }

}

?>
