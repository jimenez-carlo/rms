<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repo extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Sales_model', 'sales');
    $this->load->model('Repo_model', 'repo');
  }

  private $jsversion = 'v1.0.0';

  public function index() {

    $this->access(17);
    $this->header_data('title', 'Repo Registation');

    $repo_inventory = [];
    $repo_all = $this->repo->all();
    foreach ($repo_all as $repo) {
      $expiry = $this->repo->expiration($repo['registration_date']);
      $repo['status'] = $expiry['status'];
      $repo['message'] = $expiry['message'];
      $repo_inventory[] = $repo;
    }
    $data['repo_inventory'] = $repo_inventory;
    //echo '<pre>'; var_dump($data); echo '</pre>'; die();

    //echo '<pre>'; var_dump($this->db->last_query()); echo '</pre>';
    //echo '<pre>'; var_dump($data['repo_sales']); echo '</pre>'; die();
    $this->template('repo/inventory.php', $data);
  }

  public function in() {
    $this->access(17);
    $this->header_data('title', 'Repo Inventory');
    $this->header_data('nav', 'repo-inventory');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');
    $this->template('repo/in.php', []);
  }

  public function registration($repo_sales_id) {
    $this->header_data('title', 'Repo Registration');
    $this->header_data('nav', 'repo-registration');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');

    if ($this->input->post('save')) {
      $check_boxes = [ set_checkbox('regn_type[transfer]', '2'), set_radio('sold', 'yes') ];

      if (in_array(' checked="checked"', $check_boxes)) {
        $data['disable'] = '';
      }

      $validation = [
        [ 'field' => 'regn_type[]', 'label' => 'Registration Type', 'rules' => 'required' ],
        [ 'field' => 'repo_sales[registration_amt]', 'label' => 'Registration Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_sales[insurance_amt]', 'label' => 'Insurance Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_sales[emission_amt]', 'label' => 'Emission Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_sales[date_regn]', 'label' => 'Registration Date', 'rules' => 'required' ]
      ];

      if (isset($this->input->post('regn_type')['transfer']) || $this->input->post('sold') === 'yes') {
        $validation[] = [ 'field' => 'rsf', 'label' => 'RSF#', 'rules' => 'required' ];
        $validation[] = [ 'field' => 'cust_code', 'label' => 'Customer Code', 'rules' => 'required' ];
        $validation[] = [ 'field' => 'first_name', 'label' => 'First Name', 'rules' => 'required' ];
        $validation[] = [ 'field' => 'last_name', 'label' => 'Last Name', 'rules' => 'required' ];
        $validation[] = [ 'field' => 'ar_num', 'label' => 'AR Number', 'rules' => 'required' ];
        $validation[] = [ 'field' => 'ar_amt', 'label' => 'Amount Given', 'rules' => 'required|numeric|greater_than_equal_to[0]' ];
      }

      $this->form_validation->set_rules($validation);
      if ($this->form_validation->run()) {
        $this->repo->save_repo_sales($repo_sales_id, $this->input->post('repo_sales'), $this->input->post('sold'));
      }

    }

    $select_clause = <<<SQL
      rs.repo_sales_id, e.*,
      rs.rsf_num, rs.cid AS cid,
      rsc.cust_code AS cust_code,
      rsc.first_name AS first_name,
      rsc.last_name AS last_name,
      rs.bcode AS bcode, IFNULL(rs.bname, '') AS bname,
      rs.registration_amt, rs.emission_amt, rs.insurance_amt,
      DATE_FORMAT(rs.date_sold, '%Y-%m-%d') AS date_sold,
      DATE_FORMAT(rs.date_regn, '%Y-%m-%d') AS date_regn
SQL;
      $where_clause = <<<SQL
        rs.repo_sales_id = '{$repo_sales_id}'
SQL;

    $data['repo'] = $this->repo->get_sales($select_clause, $where_clause);
    $expire = $this->repo->expiration($data['repo']['date_regn']);

    $data['repo']['expire_status'] = $expire['status'];
    $data['repo']['expire_message'] = $expire['message'];
    $data['disable'] = 'disabled';

    //$this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?v1.0.0"></script>');
    $this->template('repo/registration.php', $data);
  }

  public function get_sales() {
    if($this->input->post('engine_no')) {
      $select_clause = <<<SELECT
        e.*,
        s.sid, s.cr_no,
        DATE_FORMAT(IFNULL(rs.registration_date, s.registration_date), '%Y-%m-%d') AS registration_date,
        IFNULL(rs.cid,s.customer) AS cid,
        IFNULL(rsc.cust_code, sc.cust_code) AS cust_code,
        IFNULL(rsc.first_name, sc.first_name) AS first_name,
        IFNULL(rsc.last_name, sc.last_name) AS last_name,
        ,rs.repo_sales_id,
        IFNULL(rs.bcode,s.bcode) AS bcode, IFNULL(rs.bname, '') AS bname,
        DATE_FORMAT(IFNULL(rs.date_sold, s.date_sold), '%Y-%m-%d') AS date_sold
SELECT;
      $where_clause = <<<WHERE
        e.engine_no = '{$this->input->post("engine_no")}'
        AND (s.sid IS NULL OR s.status >= 4)
WHERE;

      $sales = $this->repo->get_sales($select_clause, $where_clause);

      $output = [];
      if (empty($sales)) {
        $output['error'] = 'Engine number not found.';
      } elseif($sales['bcode'] === $_SESSION['branch_code']) {
        $output['error'] = 'You already claimed the engine.';
      } else {
        $url = base_url().'repo/claim';
        $this->session->set_flashdata([
          'repo' => [
            'cid' => $sales['cid'],
            'eid' => $sales['eid'],
            'date_sold' => $sales['date_sold']
          ]
        ]);
        $expire = $this->repo->expiration($sales['registration_date']);
        $xpr_msg = ($expire['status'] === 'success') ? 'Expire on' : '';
        $form = <<<HTML
          <form class="form-inline span6 offset3" method="post" action="{$url}">
            <fieldset>
              <legend>Details</legend>
                <div class="row">
                  <div class="control-group offset1">
                    <label>Branch</label>
                    <input type="text" value="" disabled>
                    <!-- <input id="customer-id" type="hidden" name="cid" type="text" value="{$sales['cid']}"> -->
                  </div>
                </div>
                <div class="row">
                  <div class="control-group offset1">
                    <label for="get-cust">Customer Code</label>
                    <input id="get-cust" type="text" value="{$sales['cust_code']}" disabled>
                    <!-- <input id="customer-id" type="hidden" name="cid" type="text" value="{$sales['cid']}"> -->
                  </div>
                </div>
                <div class="form-inline row">
                  <div class="control-group span4 offset1">
                    <label class="control-label">First Name</label>
                    <div class="controls">
                      <input id="first-name" type="text" value="{$sales['first_name']}" disabled>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Last Name</label>
                    <div class="controls">
                      <input id="last-name" type="text" value="{$sales['last_name']}" disabled>
                    </div>
                  </div>
                </div>
                <br>
                <div class="form-inline row">
                  <div class="control-group span4 offset1">
                    <label class="control-label">Engine#</label>
                    <div class="controls">
                      <input  id="engine-no" type="text" name="engine_no" value="{$sales['engine_no']}" disabled>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Chassis#</label>
                    <div class="controls">
                      <input type="text" value="{$sales['chassis_no']}" disabled>
                    </div>
                  </div>
                </div>

                <div class="form-inline row">
                  <div class="control-group span4 offset1">
                    <label class="control-label">MAT#</label>
                    <div class="controls">
                      <input type="text" value="{$sales['mat_no']}" disabled>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">MVF No.</label>
                    <div class="controls">
                      <input type="text" value="{$sales['mvf_no']}" disabled>
                    </div>
                  </div>
                </div>

                <div class="form-inline row">
                  <div class="control-group span4 offset1">
                    <label class="control-label" for="date-sold">Date Sold</label>
                    <div class="controls">
                      <input class="datepicker" type="text" value="{$sales['date_sold']}" disabled>
                    </div>
                  </div>
                  <div class="control-group {$expire['status']}">
                    <label class="control-label" for="date-regn">Date Registered</label>
                    <div class="controls">
                      <input id="date-regn" class="datepicker" type="text" name="regn-date" value="{$sales['registration_date']}" autocomplete="off">
                      <span class="help-inline">{$xpr_msg} {$expire['message']}.</span>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <button class="btn btn-success offset1" type="submit" name="save" value="true">Save</button>
                </div>
            </fieldset>
          </form>
HTML;
        $output['form'] = $form;
      }
      //$this->session->set_flashdata(['repo' =>  $repo_engines]);
      $output['log'] = $this->session->flashdata('repo');
      //$interval = date_diff('2020-01-01', date('Y-m-d'));
      //$output['log'] = [$date_expired->format('Y-m-d'), $now->format('Y-m-d'), $registration_date];
      echo json_encode($output);
    }
  }

  public function claim() {
    $this->access(17);
    if ($this->input->post('save')) {
      $this->repo->claim();
      $eid = $_SESSION['repo']['eid'];
      $this->repo->insert_history($eid);
    }

    redirect('./repo/in');
  }

  public function customer() {
    $this->access(17);
    if ($this->input->post('cust_code')) {
      $customer = $this->sales->get_customer($this->input->post());
      echo json_encode($customer);
    }
  }

  public function save_registration() {
    //$validation = [
    //  [ 'field' => 'regn_type[]', 'labels' => 'Registration Type', 'rules' => 'required' ],
    //  [ 'field' => 'registration', 'labels' => 'Registration Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
    //  [ 'field' => 'insurance', 'labels' => 'Insurance Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
    //  [ 'field' => 'emission', 'labels' => 'Emission Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
    //  [ 'field' => 'regn_date', 'labels' => 'Registration Date', 'rules' => 'required' ],
    //];
    //$this->form_validation->set_rules($validation);

    //if ($this->form_validation->run() == false) {
    //  $warnings = explode("\n",validation_errors());
    //  array_pop($warnings);
    //  $_SESSION['warning'] = $warnings;
    //}
    //redirect($_SERVER['HTTP_REFERER']);
    //echo '<pre>'; var_dump($_POST); echo '</pre>'; die();
  }

}


