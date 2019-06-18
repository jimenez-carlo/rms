<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Topsheet extends MY_Controller {

        public function __construct() {
          parent::__construct();
          $this->load->helper('url');
          $this->load->model('Topsheet_model', 'topsheet');
          $this->load->model('File_model', 'file');
          $this->load->model('Orcr_checking_model', 'orcr_checking');
          if ($_SESSION['company'] == 8) {
            $this->topsheet->region = $this->topsheet->mdi_region;
          }
        }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Topsheet');
		$this->header_data('nav', 'topsheet');
		$this->header_data('dir', './');

		$param = new Stdclass();
		$param->region = $_SESSION['region'];
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');
		$param->company = $this->input->post('company');
		$param->status = $this->input->post('status');

		$data['table'] = $this->topsheet->list_topsheet($param);
		$data['status'] = $this->topsheet->status;
		$this->template('topsheet/list', $data);
	}

	public function review()
	{
		$this->access(1);
		$this->header_data('title', 'Review Topsheet');
		$this->header_data('nav', 'topsheet');
		$this->header_data('dir', './../');

		$tid = $this->input->post('tid');

		$review = $this->input->post('review');
		if (!empty($review)) $tid = current(array_keys($review));

		if (empty($tid)) {
			redirect('topsheet');
		}
		else {
			$data['tid'] = $tid;
		}

		$data['region'] = $_SESSION['region'];
		$data['mid'] = $this->input->post('mid');
		$data['summary'] = $this->input->post('summary');
		$tid = $this->input->post('tid');
		$data['submit_all'] = $this->input->post('submit_all');
		$data['back'] = $this->input->post('back');
 		$back_key = (!empty($data['back'])) ? current(array_keys($data['back'])) : 0;

		if (!empty($data['submit_all'])) {
			$this->topsheet->hyper_save($data);
			redirect('topsheet');
		}

		if (empty($data['summary']) || $back_key == 1) {
			$data['mid'] = null;
	    $data['topsheet'] = $this->topsheet->pre_load($data);
	    $data['miscs'] = $this->topsheet->list_miscs($data);
	    $this->template('topsheet/misc', $data);
		}
		else {
	    $data['topsheet'] = $this->topsheet->load_topsheet($data);
	    $data['miscs'] = $this->topsheet->list_miscs($data);
	    $this->template('topsheet/review', $data);
		}
	}

	public function view()
	{
		$this->access(1);
		$this->header_data('title', 'Topsheet');
		$this->header_data('nav', 'topsheet');
		$this->header_data('dir', './../');

		// $view = $this->input->post('view_ts');
		// if (empty($view)) redirect('topsheet');
		// $tid = current(array_keys($view));

		$tid = $this->input->post('tid');
		if (empty($tid)) redirect('topsheet');

		$data['topsheet'] = $this->topsheet->view_topsheet($tid);
		$data['regions'] = $this->topsheet->region;
	  $this->template('topsheet/view', $data);
	}

	public function sprint()
	{
		// $data['tid'] = $this->input->post('tid');
		// $data['mid'] = $this->input->post('mid');
		// $data['topsheet'] = $this->topsheet->load_print($data);

		$tid = $this->input->post('tid');
		if (empty($tid)) redirect('topsheet');

		$data['topsheet'] = $this->topsheet->view_topsheet($tid);
		$data['regions'] = $this->topsheet->region;
		$this->load->view('topsheet/print', $data);
	}

	public function transmittal() {
		// $transmit = $this->input->post('transmit');
		// if (empty($transmit)) redirect('topsheet');
		// $tid = current(array_keys($transmit));

		$tid = $this->input->post('tid');
                if (empty($tid)) {
                  redirect('topsheet');
                }
                $data['topsheet'] = $this->topsheet->transmit_topsheet($tid);
                $this->load->view('topsheet/transmittal_print', $data);
	}

        public function request($tid) {
          if ($this->topsheet->request_reprint($tid)) {
            $_SESSION['messages'][] = 'Request for reprint sent.';
          }
          else {
            $_SESSION['warning'][] = 'Request for reprint already sent.';
          }
          redirect('topsheet');
        }

        public function create() {
          $this->access(1);
          $this->header_data('title', 'Create Topsheet');
          $this->header_data('nav', 'topsheet');
          $this->header_data('dir', './../');

          $data['region'] = $_SESSION['region'];
          $data['rid'] = $this->input->post('rid');
          $data['tot_amt'] = $this->input->post('tot_amt');
          $data['tot_exp'] = $this->input->post('tot_exp');
          $data['mid'] = $this->input->post('mid');
          $data['summary'] = $this->input->post('summary');
          $data['submit_all'] = $this->input->post('submit_all');
          $data['back'] = $this->input->post('back');
          $back_key = (!empty($data['back'])) ? current(array_keys($data['back'])) : 0;

          if (!empty($data['submit_all'])) {
            $this->topsheet->hyper_create($data);
            redirect('topsheet');
          }

          if (empty($data['rid']) || $back_key == 1) {
            $data['table'] = $this->topsheet->list_rerfo_for_topsheet();
            $data['regions'] = $this->topsheet->region;
            $data['trans_no'] = 'T-'.$this->topsheet->reg_code[$_SESSION['region']].'-'.date('ymd');
            $this->template('topsheet/create', $data);
          } else if (empty($data['summary']) || $back_key == 2) {
            $data['miscs'] = $this->topsheet->list_misc_for_topsheet($data);
            $this->template('topsheet/create_misc', $data);
          } else {
            $data['topsheet'] = $this->topsheet->list_summary_for_topsheet($data);
            $data['regions'] = $this->topsheet->region;
            $data['trans_no'] = 'T-'.$this->topsheet->reg_code[$_SESSION['region']].'-'.date('ymd');
            $this->template('topsheet/create_summary', $data);
          }
        }
}
