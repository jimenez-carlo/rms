<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nru extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('Nru_model', 'nru');
  }

        public function index()
        {
                $this->access(11);
                $this->header_data('title', 'NRU');
                $this->header_data('nav', 'nru');
                $this->header_data('dir', './');

                $data['region'] = $_SESSION['region_id'];
                $data['ltid'] = $this->input->post('ltid');
                $data['company'] = $this->input->post('company');
                $data['registration'] = $this->input->post('registration');
                $data['change_payment_method'] = $this->input->post('change_payment_method');
                $data['fund'] = $this->input->post('fund');
                $data['total_mc'] = $this->input->post('total_mc');
                $data['total_regn'] = $this->input->post('total_regn');
                $data['cash'] = $this->input->post('cash');
                $data['check'] = $this->input->post('check');
                $data['submit_all'] = $this->input->post('submit_all');
                $data['back'] = $this->input->post('back');
                $back_key = (!empty($data['back'])) ? current(array_keys($data['back'])) : 0;

                if (!empty($data['submit_all'])) $this->save_nru($data);

                if (empty($data['ltid'])) {
                        $data['table'] = $this->nru->load_list($data);
                        $this->template('nru/list', $data);
                } else {
                        $data['ltid'] = (is_array($data['ltid'])) ? current(array_keys($data['ltid'])) : $data['ltid'];
                        if (empty($data['registration']) || $back_key == 1) {
                          if (!empty($data['change_payment_method'])) {
                            $this->nru->update_payment_method($data['change_payment_method']);
                            $data['action'] = 'View';
                          } else {
                            $data['action'] = current($this->input->post('ltid'));
                          }

                          $data['transmittal'] = $this->nru->load_sales($data);
                          if (empty($data['transmittal'])) {
                            redirect('nru');
                          }

                          $this->template('nru/registration', $data);
                        } else {
                                $data['total_mc'] = 0;
                                $data['total_regn'] = 0;
                                foreach ($data['registration'] as $sid => $registration)
                                {
                                        if ($registration == 0) {
                                                unset($data['registration'][$sid]);
                                                continue;
                                        }
                                        $data['total_mc']++;
                                        $data['total_regn'] += $registration;
                                }
                                $data['cash'] = $this->nru->get_cash($data);

                                $data['account'] = $this->nru->get_account($data);
                                $this->template('nru/summary', $data);
                        }
                }
        }

        public function save_nru($data)
        {
                $this->load->model('Login_model', 'login');
                $this->db->trans_start();
                foreach ($data['registration'] as $sid => $registration)
                {
                  if ($registration == 0) continue;

                  $sales = new Stdclass();
                  $sales->sid = $sid;
                  $sales->registration = $registration;
                  $sales->status = 3;
                  $this->db->update('tbl_sales', $sales, array('sid' => $sales->sid));

                  $engine_no = $this->db->query("SELECT engine_no FROM tbl_engine INNER JOIN tbl_sales on eid = engine WHERE sid = ".$sales->sid)->row()->engine_no;
                  $this->login->saveLog('Saved Registration Expense [Php '.$registration.'] for Engine # '.$engine_no.' ['.$sid.']');
                }
                $this->db->query("
                  UPDATE
                    tbl_fund
                  SET
                    cash_on_hand = cash_on_hand - {$data['total_regn']}
                  WHERE
                    region = {$data['region']}
                ");
                $new= $this->db->query("
                  SELECT fund, cash_on_hand FROM tbl_fund WHERE region = {$data['region']}
                ")->row_array();

                $this->db->query("
                  INSERT INTO
                    tbl_fund_history(fund, out_amount, new_fund, new_hand, new_check, type)
                  VALUES ({$data['region']}, {$data['total_regn']}, {$new['fund']}, {$new['cash_on_hand']}, 0, 5)
                ");
                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                  $transmittal = $this->db->query("SELECT code FROM tbl_lto_transmittal WHERE ltid = ".$data['ltid'])->row();
                  $_SESSION['messages'][] = "Transmittal # ".$transmittal->code." updated successfully.";
                } else {
                  $_SESSION['messages'][] = "Something went wrong.";
                }
                redirect('nru');
        }

        public function nru()
        {
                $this->access(11);
                $this->header_data('title', 'NRU');
                $this->header_data('nav', 'nru');
                $this->header_data('dir', './');

                $data['region'] = $_SESSION['region_id'];
                $data['company'] = $this->input->post('company');
                $data['ltid'] = $this->input->post('ltid');
                $data['registration'] = $this->input->post('registration');
                $data['fund'] = $this->input->post('fund');
                $data['total_mc_cash'] = $this->input->post('total_mc_cash');
                $data['total_mc_check'] = $this->input->post('total_mc_check');
                $data['total_regn_cash'] = $this->input->post('total_regn_cash');
                $data['total_regn_check'] = $this->input->post('total_regn_check');
                $data['cash'] = $this->input->post('cash');
                $data['check'] = $this->input->post('check');
                $data['submit_all'] = $this->input->post('submit_all');
                $data['back'] = $this->input->post('back');
                $back_key = (!empty($data['back'])) ? current(array_keys($data['back'])) : 0;

                if (!empty($data['submit_all'])) {
                        $this->nru->save_nru($data);
                        redirect('nru');
                }

                if (empty($data['ltid'])) {
                        $data['table'] = $this->nru->load_list($data);
                        $this->template('nru/list', $data);
                }
                else {
                        if (is_array($data['ltid'])) {
                                $data['ltid'] = current(array_keys($data['ltid']));
                        }

                        if (empty($data['registration']) || $back_key == 1) {
                                $data['transmittal'] = $this->nru->load_sales($data);
                                $this->template('nru/registration', $data);
                        }
                        else {
                                if (empty($data['cash'])) {
                                        $data['total_mc_cash'] = 0;
                                        $data['total_mc_check'] = 0;
                                        $data['total_regn_cash'] = 0;
                                        $data['total_regn_check'] = 0;
                                        foreach ($data['registration'] as $sid => $registration)
                                        {
                                                if ($registration == 0) {
                                                        unset($data['registration'][$sid]);
                                                        unset($data['fund'][$sid]);
                                                        continue;
                                                }
                                                if ($data['fund'][$sid] == 2) {
                                                        $data['total_mc_cash']++;
                                                        $data['total_regn_cash'] += $registration;
                                                }
                                                else {
                                                        $data['total_mc_check']++;
                                                        $data['total_regn_check'] += $registration;
                                                }
                                        }
                                        $data['cash'] = $this->nru->get_cash($data);
                                }

                                if ((empty($data['check']) && $data['total_mc_check'] > 0)
                                        || $back_key == 2) {
                                        $data['table'] = $this->nru->list_check($data);
                                        $this->template('nru/check', $data);
                                }
                                else {
                                        if (!empty($data['check'])) $data['check'] = $this->nru->load_check($data);
                                        $data['account'] = $this->nru->get_account($data);
                                        $this->template('nru/summary', $data);
                                }
                        }
                }
        }
}
