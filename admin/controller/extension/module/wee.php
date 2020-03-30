<?php
class ControllerExtensionModuleWee extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/wee');

		$this->document->setTitle($this->language->get('heading_title'));
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if(isset($this->request->post["pin-id"])){
				$id     = $this->request->post['pin-id'];
				$serial = $this->request->post['serial'];
				$col1   = $this->request->post['col1'];
				$col2   = $this->request->post['col2'];
				$status = $this->request->post['status'];

				$sql = "update `oc_product_pin_items` set serial='{$serial}', col1='{$col1}', col2='{$col2}', status='{$status}' where id = $id";
				$this->db->query($sql);

				$this->update_balance($this->request->request['product_id']);

				$this->response->redirect($this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&product_id='.$this->request->request['product_id'], true));
				return;
			}else
			if(isset($this->request->post["pin-form"])){
				$where = "";
				foreach($this->request->post['select_item'] as $result){
					$where .= $result . ",";
				}
				$where = "($where 0)";
				$sql = "delete from oc_product_pin_items where id in $where";
				$this->db->query($sql);

				$this->update_balance($this->request->request['product_id']);

				$this->response->redirect($this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&product_id='.$this->request->request['product_id'], true));
				return;
			}else
			if(isset($this->request->post["upload-pin"])){
				$product_id = $this->request->request['product_id'];
				$status = $this->request->post['status'];

				if($this->request->files['wee_serial']['type']=='text/csv'){
					$file_path = $this->request->server['DOCUMENT_ROOT'].'/upload.csv';
					copy($this->request->files['wee_serial']['tmp_name'], $file_path);

					$handle = fopen($file_path, "r");
					if ($handle) {
						while (($line = fgets($handle)) !== false) {
							$sql = "insert into oc_product_pin_items (product_id, serial, col1, col2, status) values ($product_id, $line, '$status')";
							$query = $this->db->query($sql);
						}
						fclose($handle);
					} else {
						//error file opening
					} 
				}

				$this->update_balance($this->request->request['product_id']);

				$this->response->redirect($this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&product_id='.$product_id, true));
				return;
			}else
			if(isset($this->request->post["add-pin"])){
				$product_id = $this->request->request['product_id']; 
				
				$serial = $this->request->post['wee_serial'];
				$col1   = $this->request->post['wee_col1'];
				$col2   = $this->request->post['wee_col2'];

				$sql = "insert into oc_product_pin_items (product_id, serial, col1, col2, status) values ($product_id, '$serial', '$col1', '$col2', 'READY')";
				$query = $this->db->query($sql);

				$this->update_balance($this->request->request['product_id']);

				$this->response->redirect($this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&product_id='.$product_id, true));

				return;
				exit;
			}else
			if(isset($this->request->post["add-product"])){
				$product_id = (int)$this->request->post["product_id"];
				$sql = "insert into ".DB_PREFIX."product_pin (product_id) values ($product_id)";
				$query = $this->db->query($sql);

				$this->update_balance($this->request->request['product_id']);

				$this->response->redirect($this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module', true));
				return;
			}
			// echo "POST Executed....";
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit']     = $this->language->get('text_edit');
		$data['button_save']   = $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&store_id=', true)
		);

		$data['action'] = $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
		$data['token'] = $this->session->data['token'];


		// #############################################################################################
		// header and footer load
		// #############################################################################################
		$this->load->model('tool/image');
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		if(isset($this->request->request['product_id'])){
			$product_id = (int)$this->request->request['product_id'];

			$data['cancel'] = $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module', true);

			$data['link_all']     = $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&product_id='.$product_id, true);
			$data['link_ready']   = $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&show=ready&product_id='.$product_id, true);
			$data['link_selled']  = $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&show=selled&product_id='.$product_id, true);
			$data['link_disable'] = $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&show=disable&product_id='.$product_id, true);

			if(isset($this->request->request['action'])){
				if($this->request->request['action']=='delete'){
					$pin_id = (int)$this->request->request['pin_id'];
					$sql = "delete from oc_product_pin_items where id = $pin_id";
					$this->db->query($sql);

					$this->update_balance($this->request->request['product_id']);

					$this->response->redirect($this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&type=module&product_id='.$product_id, true));
				}
			}

			$data['products'] = array();

			$where = ""; 
			if(isset($this->request->request['show'])){
				if(strtoupper($this->request->request['show'])=='READY'){
					$where .= " and status = 'READY'";
				}else
				if(strtoupper($this->request->request['show'])=='SELLED'){
					$where .= " and status = 'SELLED'";
				}else
				if(strtoupper($this->request->request['show'])=='DISABLE'){
					$where .= " and status = 'DISABLE'";
				}
			}

			if(isset($this->request->post["form-order-id"])){
				$where .= " and order_id = ".$this->request->post["order_id"]; 
			}
			if(isset($this->request->post["form-pin"])){
				$where .= " and id = ".$this->request->post["pin_id"]; 
			}

			$sql = "SELECT * FROM oc_product_pin_items where product_id = $product_id $where";
			$query = $this->db->query($sql);
			$result_pin_items = $query->rows;

			$data['pins'] = array();
			foreach($result_pin_items as $result){
				$data['pins'][] = array(
								'id' => $result['id'],
								'product_id' => $result['product_id'],
								'serial'     => $result['serial'],
								'col1'       => $result['col1'],
								'col2'       => $result['col2'],
								'status'     => $result['status'],
								'sms_result' => $result['sms_result'],
								'sms_result' => $result['sms_result'],
								'sell_dt'    => $result['sell_dt'],
								'delete'     => $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . '&action=delete&pin_id=' . $result['id'], true), 
								'edit'       => $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . '&action=edit&pin_id=' . $result['id'], true),
				);
			}

			$sql = 'Select * from '.DB_PREFIX.'product a Join oc_product_description b On a.product_id = b.product_id Where a.product_id = '.$product_id;

			$query = $this->db->query($sql);
			$result_products = $query->rows;

			foreach ($result_products as $result) {
				if (is_file(DIR_IMAGE . $result['image'])) {
					$image = $this->model_tool_image->resize($result['image'], 40, 40);
				} else {
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
				$data['products'][] = array(
										'product_id' => $result['product_id'],
										'name'       => $result['name'],
										'model'      => $result['model'],
										'price'      => $result['price'],
										'image'      => $image 
									);
			}
			$this->response->setOutput($this->load->view('extension/module/wee_items', $data));
			return;
		}

		// #############################################################################################
		// Load Products
		// #############################################################################################
		$data['products'] = array();
		$sql = 'Select * from '.DB_PREFIX.'product a Join oc_product_description b On a.product_id = b.product_id Where a.product_id not in (Select product_id from '.DB_PREFIX.'product_pin)';
		$query = $this->db->query($sql);
		$result_products = $query->rows;
		foreach ($result_products as $result) {
			$data['products'][] = array(
									'product_id' => $result['product_id'],
									'name'       => $result['name'],
									'model'      => $result['model'],
									'price'      => $result['price'],
									'edit'       => $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'], true)
								);
		}

		// #############################################################################################
		// Load Product Pins
		// #############################################################################################

		$data['pins'] = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_pin a Join " . DB_PREFIX . "product b On a.product_id = b.product_id Left Join oc_product_description c On b.product_id = c.product_id ORDER BY a.id");
		$result_product_pins = $query->rows;
		foreach ($result_product_pins as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
			$special = false;

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);
			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}

			$query = $this->db->query("SELECT count(*) as cnt FROM " . DB_PREFIX . "product_pin_items a Where status = 'SELLED' and a.product_id = ".$result['product_id']);
			$product_pin_selled = $query->rows;
			$product_pin_selled = $product_pin_selled[0]['cnt'];

			$query = $this->db->query("SELECT count(*) as cnt FROM " . DB_PREFIX . "product_pin_items a Where status = 'READY' and a.product_id = ".$result['product_id']);
			$product_pin_ready = $query->rows;
			$product_pin_ready = $product_pin_ready[0]['cnt'];

			$query = $this->db->query("SELECT count(*) as cnt FROM " . DB_PREFIX . "product_pin_items a Where status = 'DISABLED' and a.product_id = ".$result['product_id']);
			$product_pin_disabled = $query->rows;
			$product_pin_disabled = $product_pin_disabled[0]['cnt'];

			$data['pins'][] = array(
									'product_id' => $result['product_id'],
									'image'      => $image,
									'name'       => $result['name'],
									'model'      => $result['model'],
									'price'      => $result['price'],
									'special'    => $special,
									'quantity'   => $result['quantity'],
									'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
									'edit'       => $this->url->link('extension/module/wee', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'], true),
									'pin_selled' => $product_pin_selled,
									'pin_ready'  => $product_pin_ready,
									'pin_disabled' => $product_pin_disabled,
								);
		}

		// #############################################################################################
		// Render Template
		// #############################################################################################
		$this->response->setOutput($this->load->view('extension/module/wee.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/wee')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// if (!$this->request->post['add-product']) {
		// 	$this->error['code'] = $this->language->get('error_code');
		// }			

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/event');
		$this->model_extension_event->addEvent('wee', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/wee/after_order_add');
	}
	public function uninstall() {
		$this->load->model('extension/event');
		$this->model_extension_event->deleteEvent('wee');
	}

	public function update_balance($product_id){
		$sql = "Update `oc_product` Set quantity = (Select count(*) from `oc_product_pin_items` Where status = 'READY' and product_id = {$product_id}) Where product_id = {$product_id}";
		$this->db->query($sql);
	}	
}
