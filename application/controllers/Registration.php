<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registration extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Registration_model', 'registration');
		$this->load->model('Sales_model', 'sales');
		$this->load->model('File_model', 'file');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Registration');
		$this->header_data('nav', 'registration');
		$this->header_data('dir', './');

		$data['sid'] = $this->input->post('sid');

		// on upload
		$data['temp'] = array();
		$upload = $this->input->post('upload');
		if (!empty($upload)) {
			$data['temp'] = $this->upload();
		}

		// on save
		$submit = $this->input->post('submit');
		if (!empty($submit)) {
			$sid = $this->input->post('sid');
			$sales = $this->db->query("select * from tbl_sales
				inner join tbl_engine on eid = engine
				inner join tbl_customer on cid = customer
				where sid = ".$sid)->row();
			$this->submit_validate($sales);
		}

		// on search
		$engine_no = $this->input->post('engine_no');
		if (!empty($engine_no)) {
			$data['sid'] = $this->sales->search_engine($engine_no);
			if (empty($data['sid'])) $_SESSION['warning'][] = 'Engine # '.$engine_no.' is invalid.';
		}

		$da_resolve = 0;
		if (!empty($data['sid'])) {
			$data['sales']  = $this->sales->load_sales($data['sid']);
			$da_resolve     = ($data['sales']->da_reason > 0);
		}

		return ($da_resolve) ? $this->template('registration/da_resolve', $data) : $this->template('registration/view', $data);
	}

	public function pending_list()
	{
		$this->access(1);
		$this->header_data('title', 'Registration');
		$this->header_data('nav', 'registration');
		$this->header_data('dir', './../');

		$data['region'] = $_SESSION['region'];
		$data['table'] = $this->registration->list_sales($data);
		$this->template('registration/sales', $data);
	}

	public function upload()
	{
		$temp = array();
		$total_size = 0;
		$content = '';

		if (isset($_FILES['scanFiles']))
		{
			array_multisort($_FILES['scanFiles']['name'], SORT_ASC, SORT_STRING);
			foreach ($_FILES['scanFiles']['name'] as $key => $val) {
				$total_size += $_FILES['scanFiles']['size'][$key];
			}

			if($total_size > 1000000) {
				$_SESSION['warning'][] = "The file you are attempting to upload is larger than the permitted size.";
			}
			else {
				$files = $this->file->upload_multiple();
				if (!empty($files)) {
					foreach ($files as $file) {
						$temp[] = $file->filename;
						$content .= '
							<div class="attachment temp" style="position:relative">
								'.form_hidden('temp[]', $file->filename).'
								<img src="'.$file->path.'" style="margin:5px; border:solid">
								<a style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>
							</div>';
					}
				}
			}
		}
		return $temp;
	}

	public function delete()
	{
		$file = '/temp/'.$this->input->post('filename');
		$this->file->delete($file);
	}

	public function submit_validate($sales)
        {
          $err_msg = array();
          $files = $this->input->post('files');
          $temp = $this->input->post('temp');
          if (empty($files) && empty($temp)) $err_msg[] = 'File attachment is required.';

          switch ($sales->da_reason) {
            case 1: // wrong amount
              $this->form_validation->set_rules('registration', 'Registration', 'required|is_numeric|non_zero');
              break;

            case 2: // no si/ar
            case 3: // invalid si/ar
              $this->form_validation->set_rules('si_no', 'SI #', 'required');
              $this->form_validation->set_rules('ar_no', 'AR #', 'required');
              break;

            case 4:  // unreadable attachment
            case 5:  // missing or
            case 6:  // mismatch cust name
            case 7:  // mismatch engine
            case 10: // wrong regn type
              $form_valid = TRUE;
              break;

            case 8: // mismatch cr
              $cr_no = $this->input->post('cr_no');
              if (strlen($cr_no) != 9) $err_msg[] = 'CR # must be 9 digits.';
              $this->form_validation->set_rules('cr_no', 'CR #', 'required');
              break;

            default: // not da, normal registration
              $this->form_validation->set_rules('registration', 'Registration', 'required|is_numeric|non_zero');
              $this->form_validation->set_rules('tip', 'Tip', 'required|is_numeric');
              $this->form_validation->set_rules('cr_date', 'Registration Date', 'required');
              $this->form_validation->set_rules('cr_no', 'CR #', 'required');
              $this->form_validation->set_rules('mvf_no', 'MVF #', 'required');

              $cr_no = $this->input->post('cr_no');
              if (strlen($cr_no) != 9) $err_msg[] = 'CR # must be 9 digits.';
          }

          if (!isset($form_valid)) $form_valid = $this->form_validation->run();

          return ($form_valid && empty($err_msg)) ? $this->submit_save($sales) : $_SESSION['warning'] = $err_msg;
        }

	public function submit_save($sales)
        {
          $files = $this->input->post('files');
          $files = (!empty($files)) ? $files : array();
          $temp = $this->input->post('temp');
          $temp = (!empty($temp)) ? $temp : array();
          $this->file->save_scan_docs($sales, $files, $temp);

          // prepare save
          $new_sales = new Stdclass();

          switch ($sales->da_reason)
          {
          case 1: // wrong amount
            $new_sales->registration = $this->input->post('registration');
            $new_sales->da_reason = 11;
            $this->db->update('tbl_sales', $new_sales, array('sid' => $sales->sid));
            break;

          case 2: // no si/ar
          case 3: // invalid si/ar
            $new_sales->si_no = $this->input->post('si_no');
            $new_sales->ar_no = $this->input->post('ar_no');
            $new_sales->da_reason = 11;
            $this->db->update('tbl_sales', $new_sales, array('sid' => $sales->sid));
            break;

          case 4:  // unreadable attachment
          case 5:  // missing or
          case 6:  // mismatch cust name
          case 7:  // mismatch engine
          case 10: // wrong regn type
            $new_sales->da_reason = 11;
            $this->db->update('tbl_sales', $new_sales, array('sid' => $sales->sid));
            break;

          case 8: // mismatch cr
            $new_sales->cr_no = $this->input->post('cr_no');
            $new_sales->da_reason = 11;
            $this->db->update('tbl_sales', $new_sales, array('sid' => $sales->sid));
            break;

          default: // not da, normal registration
            $new_sales->sid = $sales->sid;
            $new_sales->registration = $this->input->post('registration');
            $new_sales->tip = $this->input->post('tip');
            $new_sales->cr_date = $this->input->post('cr_date');
            $new_sales->cr_no = $this->input->post('cr_no');
            $new_sales->mvf_no = $this->input->post('mvf_no');
            $new_sales->plate_no = $this->input->post('plate_no');
            $new_sales->status = 4;
            $new_sales->file = 1;
            $new_sales->registration_date = date('Y-m-d H:i:s');
            $this->sales->save_registration($new_sales);

            // for fund update
            $expense = ($sales->registration - $new_sales->registration) + ($sales->tip - $new_sales->tip);
          }

          // update fund
          if ($expense != 0) {
            $fund = $this->db->query("select * from tbl_fund
              where region = ".$_SESSION['region'])->row();

            $new_fund = new Stdclass();
            $new_fund->cash_on_hand = $fund->cash_on_hand + $expense;
            $this->db->update('tbl_fund', $new_fund, array('fid' => $fund->fid));

            $history = new Stdclass();
            $history->fund = $fund->fid;
            $history->in_amount = ($expense > 0) ? $expense : 0;
            $history->out_amount = ($expense < 0) ? $expense * 1 : 0;
            $history->new_fund = $fund->fund;
            $history->new_hand = $new_fund->cash_on_hand;
            $history->new_check = $fund->cash_on_check;
            $history->type = 6;
            $this->db->insert('tbl_fund_history', $history);
          }

          // logs & message
          $this->load->model('Login_model', 'login');
          $this->login->saveLog('[Registration] updated details for sale ['.$sales->sid.'] with Engine # '.$sales->engine_no);
          $_SESSION["messages"][] = 'Engine # '.$sales->engine_no.' for '.$sales->first_name.' '.$sales->last_name.' updated successfully.';

          // redirect to info
          redirect('sales/view/'.$sales->sid);
        }

	public function registration()
	{
		$this->access(1);
		$this->header_data('title', 'Registration');
		$this->header_data('nav', 'registration');
		$this->header_data('dir', './');

		$data['region'] = $_SESSION['region'];
		$data['ltid'] = $this->input->post('ltid');
		$data['registration'] = $this->input->post('registration');
		$data['tip'] = $this->input->post('tip');
		$data['cr_date'] = $this->input->post('cr_date');
		$data['cr_no'] = $this->input->post('cr_no');
		$data['mvf_no'] = $this->input->post('mvf_no');
		$data['plate_no'] = $this->input->post('plate_no');
		$data['expense'] = $this->input->post('expense');
		$data['submit_all'] = $this->input->post('submit_all');
		$data['back'] = $this->input->post('back');

		if (!empty($data['submit_all'])) {
			$this->registration->register_sales($data);
			redirect('registration');
		}

		if (empty($data['ltid'])) {
			$data['table'] = $this->registration->load_list($data);
			$this->template('registration/list', $data);
		}
		else {
	 		if (is_array($data['ltid'])) {
	 			$data['ltid'] = current(array_keys($data['ltid']));
	 		}

			if (!empty($data['registration'])) {
				foreach ($data['registration'] as $sid => $registration)
				{
					if (empty($data['registration'][$sid])
						|| empty($data['cr_date'][$sid])
						|| empty($data['cr_no'][$sid])
						|| empty($data['mvf_no'][$sid])) {
							unset($data['registration'][$sid]);
							unset($data['tip'][$sid]);
							unset($data['cr_date'][$sid]);
							unset($data['cr_no'][$sid]);
							unset($data['mvf_no'][$sid]);
							unset($data['plate_no'][$sid]);
						}
				}

				if (empty($data['registration'])) {
					$_SESSION['warning'][] = 'No records to update.';
				}
			}

			if (empty($data['registration']) || !empty($data['back'])) {
				$data['table'] = $this->registration->list_sales($data);
				$this->template('registration/sales', $data);
			}
			else {
				$data['transmittal'] = $this->registration->load_sales($data);
				$this->template('registration/summary', $data);
			}
		}
	}
}
