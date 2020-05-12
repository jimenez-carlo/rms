<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orcr_checking extends MY_Controller {

        public function __construct() {
                parent::__construct();
                $this->load->helper('url');
                $this->load->model('Orcr_checking_model', 'orcr_checking');
        }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'For Checking');
		$this->header_data('nav', 'orcr_checking');
		$this->header_data('dir', './');

		$data['tid'] = $this->input->post('tid');
		$data['EPP'] = $this->input->post('EPP');
		$data['CA'] = $this->input->post('CA');
		$data['sid'] = $this->input->post('sid');
		$data['mid'] = $this->input->post('mid');
		$data['summary'] = $this->input->post('summary');
		$data['submit_all'] = $this->input->post('submit_all');
		$data['back'] = $this->input->post('back');
                $data['reference_selected'] = NULL;

		if (!empty($data['submit_all'])) {
                  $data['region'] = $this->input->post('region');
                  $data['company'] = $this->input->post('company');
                  $this->save($data);
                }

		if (!empty($data['back'])) {
                  $data['sid'] = null;
                  $data['mid'] = null;
		}

                if (!empty($data['EPP']) xor !empty($data['CA'])) {
                  $data['batch_ref'] = $this->orcr_checking->get_sales($data);
                  $data['reference_selected'] = $data['batch_ref']['reference'];

                  if (isset($data['CA'])) {
                    $data['misc_expense'] = $this->orcr_checking->get_misc_expense($data);
                  } else {
                    $data['misc_expense'] = NULL;
                  }

                  $view = (!empty($data['summary'])) ? 'orcr_checking/summary' : 'orcr_checking/ca_ref';
                  $data['view'] = $this->load->view($view, $data, TRUE);
                }

                $data['ca_refs'] = $this->orcr_checking->get_ca_for_checking();

                $this->template('orcr_checking/list', $data);
        }

        public function attachment()
        {
        	$id = $this->input->post('id');
        	$type = $this->input->post('type');

        	switch ($type)
        	{
        		case 1:
        			$data['sales'] = $this->orcr_checking->sales_attachment($id);
        			$response['page'] = $this->load->view('orcr_checking/sales_attachment', $data, TRUE);
                                $response['disable'] = (!in_array($data['sales']->da_reason, array(0,11))) ? true : false;
        			break;
        		case 2:
        			$data['misc'] = $this->orcr_checking->misc_attachment($id);
        			$response['page'] = $this->load->view('orcr_checking/misc_attachment', $data, TRUE);
        			$response['disable'] = (in_array($data['misc']->status, array(3, 4, 5))) ? true : false;
        			break;
        	}

        	echo json_encode($response);
        }

        public function save($data)
        {
                $sap_upload_batch = $this->orcr_checking->sap_upload_process_all($data['region'], $data['company'], $data['sid'], $data['mid']);

                $_SESSION['messages'][] = 'Transaction # '.$sap_upload_batch.' updated successfully.';
                redirect('orcr_checking');
        }
}
