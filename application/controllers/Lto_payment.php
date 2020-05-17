<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lto_payment extends MY_Controller {

	public function __construct() {
		parent::__construct();
    		$this->load->model('Lto_payment_model', 'lto_payment');
    		$this->load->model('Js_model', 'js');

                // Check in application/core/MY_Controller the value of $this->region and $this->company
                if ($_SESSION['company'] == 8) {
                   $this->region  = $this->mdi_region;
                   $this->company = $this->mdi;
                }
	}

	public function index() {
		$this->access(1);
		$this->header_data('title', 'LTO Payment');
		$this->header_data('nav', 'lto_payment');
		$this->header_data('dir', './');

		$param = new Stdclass();
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');
		$param->region = $this->input->post('region');
		$param->status = $this->input->post('status');
		$param->reference = $this->input->post('reference');

		$data['table']   = $this->lto_payment->get_list($param);
		$data['status']  = $this->lto_payment->status;
		$data['region']  = $this->region;
		$data['company'] = $this->company;

		$this->template('lto_payment/list', $data);
	}

	public function extract() {
		$this->access(1);
		$this->header_data('title', 'LTO Payment');
		$this->header_data('nav', 'lto_payment');
		$this->header_data('dir', './../');

                $data['company'] = ($this->session->company === 'MDI') ? array(8 => 'MDI') : array(1 => 'MNC', 6 => 'MTI', 3 => 'HPTI');
		$this->template('lto_payment/extract_form', $data);
	}

        public function csv() {
          $param = new Stdclass();
          $param->region = $_SESSION['region_id'];
          $param->company = $this->input->post('company');
          $param->date_from = $this->input->post('date_from');
          $param->date_to = $this->input->post('date_to');

          $data['result'] = $this->lto_payment->extract_to_csv($param);
          $data['param'] = $param;
          $this->load->view('lto_payment/extract_csv', $data);
        }

        public function view($lpid) {
          $payment = $this->lto_payment->load_payment($lpid);

          $this->access(1);
          $this->header_data('title', $payment->reference);
          $this->header_data('nav', 'lto_payment');
          $this->header_data('dir', './../../');

          $payment->region  = $this->region[$payment->region];
          $payment->company = $this->company[$payment->company];

          $data['payment'] = $payment;
          $this->template('lto_payment/view', $data);
        }

	public function add()
	{
		$this->access(1);
		$this->header_data('title', 'Add New Batch');
		$this->header_data('nav', 'lto_payment');
		$this->header_data('dir', './../');

		$save = $this->input->post('save');
		if (!empty($save)) {
			$this->form_validation->set_rules('company', 'Company', 'required');
			$this->form_validation->set_rules('reference', 'Reference #', 'required');
			$this->form_validation->set_rules('ref_date', 'Date', 'required');
			$this->form_validation->set_rules('amount', 'Amount', 'required|is_numeric|non_zero');

			if ($this->form_validation->run()
				// && ($screenshot = $this->lto_payment->upload_screenshot())
				&& ($batch = $this->lto_payment->upload_batch())) {
				$payment = new Stdclass();
				$payment->region = $_SESSION['region_id'];
				$payment->company = $this->input->post('company');
				$payment->reference = $this->input->post('reference');
				$payment->ref_date = $this->input->post('ref_date');
				$payment->amount = $this->input->post('amount');
				// $payment->screenshot = $screenshot;
				$this->lto_payment->save_payment($payment, $batch);
			}
		}

		$data['region'] = $this->region[$_SESSION['region_id']];
		$data['company'] = array(0 => '- Please select a company -', 1 => 'MNC', 3 => 'HPTI', 6 => 'MTI');
		$this->template('lto_payment/add', $data);
	}

	public function edit($lpid) {
		$save = $this->input->post('save');
		if (!empty($save)) {
			$this->form_validation->set_rules('company', 'Company', 'required');
			$this->form_validation->set_rules('reference', 'Reference #', 'required');
			$this->form_validation->set_rules('ref_date', 'Date', 'required');
			$this->form_validation->set_rules('amount', 'Amount', 'required|is_numeric|non_zero');

			if ($this->form_validation->run()) {
				$new_payment = new Stdclass();
				$new_payment->lpid = $lpid;
				$new_payment->region = $_SESSION['region_id'];
				$new_payment->company = $this->input->post('company');
				$new_payment->reference = $this->input->post('reference');
				$new_payment->ref_date = $this->input->post('ref_date');
				$new_payment->amount = $this->input->post('amount');

				// if (isset($_FILES['screenshot'])) $new_payment->screenshot = $this->lto_payment->upload_screenshot();
				$remove = $this->input->post('remove');
				$engine_no = $this->input->post('engine_no');

				$this->lto_payment->update_payment($new_payment, $remove, $engine_no);
			}
		}

		$payment = $this->lto_payment->load_payment($lpid);

		$this->access(1);
		$this->header_data('title', 'Edit '.$payment->reference);
		$this->header_data('nav', 'lto_payment');
		$this->header_data('dir', './../../');

		$data['payment'] = $payment;
                $data['lpid'] = $lpid;
		$data['region'] = (isset($_SESSION['region_id'])) ? $this->region[$_SESSION['region_id']] : '';
		$data['company'] = array(0 => '- Please select a company -', 1 => 'MNC', 2 => 'MTI', 3 => 'HPTI');
		$this->template('lto_payment/edit', $data);
	}

	public function pending()
	{
		$this->access(1);
		$this->header_data('title', 'Pending LTO Payment');
		$this->header_data('nav', 'lto_payment');
		$this->header_data('dir', './../');

		$save = $this->input->post('save');
		if (!empty($save)) {
			$doc_no = $this->input->post('doc_no');
			if (!empty($doc_no)) {
				foreach ($doc_no as $lpid => $val) {
					if (!empty($val)) {
						$payment = new Stdclass();
						$payment->lpid = $lpid;
						$payment->doc_no = $val;
						$payment->doc_date = date('Y-m-d H:i:s');
						$this->lto_payment->update_payment_status($payment, 2);
					}
				}
				$_SESSION['messages'][] = 'Records updated successfully.';
			}
			else {
				$_SESSION['warning'][] = 'Nothing to save.';
			}
		}

		$data['table']   = $this->lto_payment->get_list_by_status(1);
		$data['region']  = $this->region;
		$data['company'] = $this->company;
		$this->template('lto_payment/pending', $data);
	}

	public function processing()
	{
		$this->access(1);
		$this->header_data('title', 'Processing LTO Payment');
		$this->header_data('nav', 'lto_payment');
		$this->header_data('dir', './../');

		$save = $this->input->post('save');
		if (!empty($save)) {
			$dm_no = $this->input->post('dm_no');
			if (!empty($dm_no)) {
				foreach ($dm_no as $lpid => $val) {
					if (!empty($val)) {
						$payment = new Stdclass();
						$payment->lpid = $lpid;
						$payment->dm_no = $val;
						$payment->dm_date = date('Y-m-d H:i:s');
						$this->lto_payment->update_payment_status($payment, 3);
					}
				}
				$_SESSION['messages'][] = 'Records updated successfully.';
			}
			else {
				$_SESSION['warning'][] = 'Nothing to save.';
			}
		}

		$data['table'] = $this->lto_payment->get_list_by_status(2);
		$data['region'] = $this->region;
		$data['company'] = $this->company;
		$this->template('lto_payment/processing', $data);
	}

        public function for_deposit() {
          $this->access(1);
          $this->header_data('title', 'For Deposit LTO Payment');
          $this->header_data('nav', 'lto_payment');
          $this->header_data('dir', './../');
          $checkboxid    = 'check-all-for-deposit';
          $checkboxclass = 'input-checkbox';

          $save = $this->input->post('save');
          if (!empty($save)) {
            $lpids = $this->input->post('lpid');
            if (!empty($lpids)) {
              $ctr = 0;
              foreach ($lpids as $lpid) {
                $payment = new Stdclass();
                $payment->lpid = $lpid;
                $payment->deposit_date = date('Y-m-d H:i:s');
                $this->lto_payment->deposit_payment($payment);
                $ctr++;
              }
              if ($ctr) $_SESSION['messages'][] = 'Records updated successfully.';
            } else {
              $_SESSION['warning'][] = 'Nothing to save.';
            }
          }

          $data['table'] = $this->lto_payment->get_list_by_status(3);
          $data['region']  = $this->region;
          $data['company'] = $this->company;
          $data['id_select_all']  = $checkboxid;
          $data['class_checkbox'] = $checkboxclass;
          $data['javascript']  = $this->js->jquery_checkall($checkboxid, $checkboxclass);
          $this->template('lto_payment/for_deposit', $data);
        }

	public function liquidation()
	{
		$this->access(1);
		$this->header_data('title', 'Liquidation LTO Payment');
		$this->header_data('nav', 'lto_payment');
		$this->header_data('dir', './../');

		$save = $this->input->post('save');
		if (!empty($save)) {
			$liquidated = $this->input->post('liquidated');
			if (!empty($liquidated)) {
				foreach ($liquidated as $lpid) {
					$payment = new Stdclass();
					$payment->lpid = $lpid;
					$payment->close_date = date('Y-m-d H:i:s');
					$this->lto_payment->update_payment_status($payment, 5);
				}
				$_SESSION['messages'][] = 'Records updated successfully.';
			}
			else {
				$_SESSION['warning'][] = 'Nothing to save.';
			}
		}

		$data['table'] = $this->lto_payment->get_liquidation_list();
		$data['region']  = $this->region;
		$data['company'] = $this->company;
		$this->template('lto_payment/liquidation', $data);
	}

	public function print_batch($lpid)
	{
		$payment = $this->lto_payment->load_batch_sales($lpid);
		$payment->region = $this->region[$payment->region];
		$payment->company = $this->company[$payment->company];
		$data['payment'] = $payment;
		$this->load->view('lto_payment/print_batch', $data);
	}
}
