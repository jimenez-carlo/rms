<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Validation_model extends CI_Model {
  function __construct() {
    parent::__construct();
    // code...
  }

  public function form(string $TRANSACTION, array $data = []) {
    switch ($TRANSACTION) {
      case 'REPO_SALES':
        $validation = [
          //REPO SALE
          [ 'field' => 'repo_sale[rsf_num]', 'label' => 'RSF#', 'rules' => 'trim|required' ],
          [ 'field' => 'repo_sale[ar_num]', 'label' => 'AR Number', 'rules' => 'trim|required' ],
          [ 'field' => 'repo_sale[ar_amt]', 'label' => 'Amount Given', 'rules' => 'trim|required|numeric|greater_than_equal_to[0]' ],
          [ 'field' => 'repo_sale[date_sold]', 'label' => 'Date Sold', 'rules' => 'trim|required' ],
          //CUSTOMER
          [ 'field' => 'customer[cust_code]', 'label' => 'Customer Code', 'rules' => 'trim|required' ],
          [ 'field' => 'customer[first_name]', 'label' => 'First Name', 'rules' => 'trim|required' ],
          [ 'field' => 'customer[last_name]', 'label' => 'Last Name', 'rules' => 'trim|required' ],
          [ 'field' => 'customer[email]', 'label' => 'Email Address', 'rules' => 'trim|required|valid_email' ],
          [ 'field' => 'customer[phone_number]', 'label' => 'Mobile No.', 'rules' => 'trim|required|numeric|exact_length[11]' ],
        ];
        break;

      case 'REPO_REGISTRATION':
        $unreceipted_renewal_tip = $data['unreceipted_renewal_tip'] ?? 0;
        $unreceipted_transfer_tip = $data['unreceipted_transfer_tip'] ?? 0;
        $unreceipted_hpg_pnp_clearance_tip = $data['unreceipted_hpg_pnp_clearance_tip'] ?? 0;
        $unreceipted_macro_etching_tip = $data['unreceipted_macro_etching_tip'] ?? 0;
        $unreceipted_plate_tip = $data['unreceipted_plate_tip'] ?? 0;
        $validation = [
          [ 'field' => 'repo_registration[date_registered]', 'label' => 'Registration Date', 'rules' => 'required' ],
          [ 'field' => 'repo_registration[orcr_amt]', 'label' => 'OR/CR Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
          [ 'field' => 'repo_registration[renewal_amt]', 'label' => 'Renewal Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
          [ 'field' => 'repo_registration[transfer_amt]', 'label' => 'Transfer Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
          [ 'field' => 'repo_registration[hpg_pnp_clearance_amt]', 'label' => 'HPG / PNP Clearance Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
          [ 'field' => 'repo_registration[insurance_amt]', 'label' => 'Insurance Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
          [ 'field' => 'repo_registration[emission_amt]', 'label' => 'Emission Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
          [ 'field' => 'repo_registration[macro_etching_amt]', 'label' => 'Macro Etching', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
          [
            'field' => 'repo_registration[renewal_tip]',
            'label' => 'Renewal Tip',
            'rules' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to['.$unreceipted_renewal_tip.']'
          ],
          [
            'field' => 'repo_registration[transfer_tip]',
            'label' => 'Transfer Tip',
            'rules' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to['.$unreceipted_transfer_tip.']'
          ],
          [
            'field' => 'repo_registration[hpg_pnp_clearance_tip]',
            'label' => 'HPG / PNP Clearance Tip',
            'rules' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to['.$unreceipted_hpg_pnp_clearance_tip.']'
          ],
          [
            'field' => 'repo_registration[macro_etching_tip]',
            'label' => 'Macro Etching Tip',
            'rules' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to['.$unreceipted_macro_etching_tip.']'
          ],
          [
            'field' => 'repo_registration[plate_tip]',
            'label' => 'Plate Tip',
            'rules' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to['.$unreceipted_plate_tip.']'
          ],
        ];
        break;

      case 'REPO_BATCH_MISC_EXP':
        $validation = [
          [ 'field' => 'repo_batch_id', 'label' => 'Reference Number', 'rules' => 'required' ],
          [ 'field' => 'expense_type', 'label' => 'Expense Type', 'rules' => 'required' ],
          [ 'field' => 'or_date', 'label' => 'OR Date', 'rules' => 'required' ],
          [ 'field' => 'or_no', 'label' => 'OR No.', 'rules' => 'required' ],
          [ 'field' => 'amount', 'label' => 'Misc Expense Amount', 'rules' => 'required' ],
        ];

        if ($data['expense_type'] === 'Others') {
          $validation[] = [ 'field' => 'others', 'label' => 'Others', 'rules' => 'required' ];
        }
        break;
    }

    $this->form_validation->set_rules($validation);
    return $this->form_validation->run();
  }

  /**
   * Validate if the date is correct
   * @return bool
   */
  public function valid_date($date) {
    $year  = (int) substr($date, 0, 4) ?? null;
    $month = (int) substr($date, 5, 2) ?? null;
    $day   = (int) substr($date, 8, 2) ?? null;

    if (checkdate($month, $day, $year)) {
      return true;
    }
    $this->form_validation->set_message('valid_date', '{field} date is invalid.');
    return false;
  }


}
