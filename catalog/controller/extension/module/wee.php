<?php
class ControllerExtensionModuleWee extends Controller {
	public function after_order_add($subsystem, $parameter)
	{
			$order_id = $parameter[0];

			$this->load->language('extension/module/wee');

			// check the ip is not from Iran

			// 
			$query = $this->db->query("Select op.product_id, op.quantity, o.customer_id, o.email, o.telephone From `oc_order_product` op join `oc_order` o on op.order_id = o.order_id Where op.product_id in (select product_id from oc_product_pin_items where status='READY') and op.order_id = $order_id");
			$results = $query->rows;

			$customer_id = isset($results[0]['customer_id']) ? $results[0]['customer_id'] : null;
			$email       = isset($results[0]['email']) ? $results[0]['email'] : null;
			$telephone   = isset($results[0]['telephone']) ? $results[0]['telephone'] : null;

			foreach($results as $result)
			{
				$vPID   = $result['product_id'];

				// Check order id sended for customer when order status changed 
				$sql       = "Select count(*) as cnt from `oc_product_pin_items` Where order_id = {$order_id} and product_id = {$vPID}";
				$queryCnt  = $this->db->query($sql);
				$resultCnt = $queryCnt->rows;
				$PIN_CNT   = $resultCnt[0]['cnt'];

				$vCount  = $result['quantity'] - $PIN_CNT;
				$vCustID = $result['customer_id'];
				$vEmail  = $result['email'];
				
				if($vCount > 0)
				{
					$sql   = "Update `oc_product_pin_items` Set order_id = $order_id, status = 'SELLED', sell_dt = now(), user_id = $vCustID where product_id = $vPID and status = 'READY' Limit $vCount";
					$query = $this->db->query($sql);
					$this->update_balance($vPID);
				}
			}

			// change status of order if all products are virtual
			$query  = $this->db->query("Select Sum(quantity) as sm from `oc_order_product` Where order_id = {$order_id} and product_id not in (Select product_id from `oc_product_pin_items` where order_id = {$order_id})");
			$result = $query->rows;
			if($result[0]['sm'] > 0){
			}else{
				$this->db->query("update `oc_order` Set order_status_id = 5 Where order_id = {$order_id}");
			}

			// Send SMS
			if($telephone != null){
				$sql = "Select concat('PIN-', ppi.id) PIN_ID, p.model, pd.name, serial, col1, col2  
						from oc_product_pin_items ppi 
						join oc_product p on ppi.product_id = p.product_id
						join oc_product_description pd on pd.product_id = p.product_id 
							where ppi.order_id = {$order_id}"; 
				
				$query = $this->db->query($sql);
				$results = $query->rows;

				$text  = $this->language->get('entry_email_title')."\n";
				$text .= "شناسه خرید: {$order_id}\n";
				foreach($results as $result)
				{
					$text .= "{$result['model']} / {$result['name']}:\n  {$result['serial']} \n  {$result['col1']} \n  {$result['col2']} \n\n";
				}

				$sms_username  = $this->language->get('sms_username');
				$sms_password  = $this->language->get('sms_password');
				$sms_number    = $this->language->get('sms_number');
				$this->sms($order_id, $telephone, $text, $sms_username, $sms_password, $sms_number);
			}

			//send email
			$mail = new Mail();
			$mail->protocol      = $this->config->get('config_mail_protocol');
			$mail->parameter     = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');

			$mail->setTo($order_info['email']);
			$mail->setFrom($this->config->get('config_email'));

			$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get('entry_email_title'), ENT_QUOTES, 'UTF-8'));

			// $mail->setHtml($this->load->view('mail/order', $data));
			// $mail->setText($text);

			$mail->send();
	}

	private function sms($order_id, $telephone, $text, $sms_username, $sms_password, $sms_number)
	{
		ini_set('soap.wsdl_cache_enabled', '0');
		$sms_client        = new SoapClient('http://87.107.121.54/post/send.asmx?wsdl', array('encoding'=>'UTF-8'));

		$param['username'] = $sms_username;
		$param['password'] = $sms_password;
		$param['from']     = $sms_number;
		$param['to']       = $telephone;
		$param['text']     = $text;
		$param['isflash']  = false;

		$sms_result        = $sms_client->SendSimpleSMS2($param)->SendSimpleSMS2Result;

		$sql   = "Update `oc_product_pin_items` Set sms_result = '{$sms_result} [{$telephone}]' where order_id = $order_id and status = 'SELLED'";
		$query = $this->db->query($sql);
	}

	public function update_balance($product_id)
	{
		$sql = "Update `oc_product` Set quantity = (Select count(*) from `oc_product_pin_items` Where status = 'READY' and product_id = {$product_id}) Where product_id = {$product_id}";
		$this->db->query($sql);
	}	

	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'custom_field'            => json_decode($order_query->row['custom_field'], true),
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => json_decode($order_query->row['payment_custom_field'], true),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => json_decode($order_query->row['shipping_custom_field'], true),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'order_status'            => $order_query->row['order_status'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified']
			);
		} else {
			return false;
		}
	}
}