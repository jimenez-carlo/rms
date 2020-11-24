<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_fund extends MY_Controller {

        public function __construct() {
                parent::__construct();
                $this->load->model('Return_fund_model', 'return_fund');
                $this->load->model('Fund_model', 'fund');
                $this->load->model('Rms_model', 'rms');
                if ($_SESSION['company'] == 8) {
                  $this->region  = $this->mdi_region;
                  $this->company = $this->mdi;
                }
        }

        public function index()
        {
                $this->access(1);
                $this->header_data('title', 'Return Fund');
                $this->header_data('nav', 'return_fund');
                $this->header_data('dir', './');
                $this->footer_data('script', '<script src="'.base_url().'vendors/datatables/js/dataTables.select.min.js"></script>');
                $this->footer_data('return_fund_js', '<script src="'.base_url().'assets/js/return_fund.js?v1.0.0"></script>');

                $param = new Stdclass();
                $param->company   = $this->input->post('company');
                $param->region    = $this->input->post('region');
                $param->status    = $this->input->post('status');
                $param->reference = $this->input->post('reference');
                $param->date_from = $this->input->post('date_from') ?: date('Y-m-d', strtotime('-7 days'));
                $param->date_to   = $this->input->post('date_to')   ?: date('Y-m-d');

                $status = $this->rms->get_statuses("CONCAT('{',GROUP_CONCAT('\"',status_id,'\"', ' : \"', status_name SEPARATOR '\",'), '\"}') AS statuses", 'RETURN_FUND');
                $company = $this->rms->get_all_company(
                  "CONCAT('{',GROUP_CONCAT('\"',cid,'\"', ' : \"', company_code SEPARATOR '\",'), '\"}') AS companies",
                  $this->return_fund->companyQry
                );
                $data['statuses'] = json_decode($status[0]['statuses'], 1);
                $data['companies'] = json_decode($company['companies'], 1);
                $data['date_from'] = $param->date_from;
                $data['date_to']   = $param->date_to;
                $data['table']     = $this->return_fund->load_list($param);
                $data['region']    = $this->region;
                $this->template('return_fund/list', $data);
        }

        public function ca($vid)
        {
                $this->access(1);
                $this->header_data('title', 'Return Fund');
                $this->header_data('nav', 'return_fund');
                $this->header_data('dir', './../../');

                $save = $this->input->post('save');
                if (!empty($save)) {
                  $cash_on_hand = $this->fund->get_cash_on_hand($_SESSION['fund_id']);
                  $this->form_validation->set_rules(
                    'amount',
                    'Amount',
                    'required|is_numeric|non_zero|less_than_equal_to['.$cash_on_hand.']',
                    array('less_than_equal_to' => 'The amount must be less than or equal to Cash on Hand.')
                  );

                  if ($this->form_validation->run() && ($slip = $this->return_fund->upload_slip())) {
                    $return = new Stdclass();
                    $return->fund = $vid;
                    $return->amount = $this->input->post('amount');
                    $return->slip = $slip;
                    $this->return_fund->save_return($return);
                  }
                }

                $data['fund'] = $this->return_fund->load_fund($vid);
                $this->template('return_fund/ca', $data);
        }

        public function view($rfid)
        {
                $this->access(1);
                $this->header_data('title', 'Return Fund');
                $this->header_data('nav', 'return_fund');
                $this->header_data('dir', './../../');
                $this->footer_data('return_fund_js', '<script src="'.base_url().'assets/js/return_fund.js?v1.0.0"></script>');

                $liquidate = $this->input->post('liquidate');
                if (!empty($liquidate)) {
                  $this->return_fund->liquidate_return($rfid);
                }

                $amount = $this->input->post('amount');
                if (!empty($amount)) {
                  $this->return_fund->correct_amount($rfid, $amount);
                }

                $data['return'] = $this->return_fund->load_return($rfid);
                if (is_null($data['return']) || $data['return']->status === 'Deleted') {
                  show_404();
                } else {
                  $this->template('return_fund/view', $data);
                }
        }

        public function disapprove($rfid)
        {
          $status_id = $this->input->post('ret_dis_status');
          $amount = $this->input->post('amount');
          $fund_id = $this->input->post('fund_id');
          switch ($status_id) {
            case '2':
            case '3':
            case '4':
            case '5':
              $this->db->trans_start();
              // Return amount to RRT Cash on Hand
              $update_cash_on_hand = "UPDATE tbl_fund SET cash_on_hand = cash_on_hand + {$amount} WHERE fid = {$fund_id}";
              $this->db->query($update_cash_on_hand);
              // Save history
              $this->return_fund->save_return_fund_history($rfid, $status_id);
              $this->db->trans_complete();
              break;
          }
          redirect($_SERVER['HTTP_REFERER']);
        }

        public function delete($rfid)
        {
          $this->db->trans_start();
          $this->db->update('tbl_return_fund', ['is_deleted' => 1], 'rfid = '.$rfid);
          $this->return_fund->save_return_fund_history($rfid, 90);
          $this->db->trans_complete();
          if ($this->db->trans_status()) {
            $_SESSION['messages'][] = 'Return fund deleted successfully.';
          } else {
            $_SESSION['warning'][] = 'Return fund wasn\'t deleted. Something went wrong.';
          }

          redirect('./return_fund');
        }


}
