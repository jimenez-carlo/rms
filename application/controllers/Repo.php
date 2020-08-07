<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repo extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Sales_model', 'sales');
    $this->load->model('Repo_model', 'repo');
  }

  public function index() {

    $this->access(17);
    $this->header_data('title', 'Repo Registation');

    //$this->header_data('nav', 'repo-registration');
    //$this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?v1.0.0"></script>');
    $data['repo_sales'] = $this->repo->all();

    //echo '<pre>'; var_dump($this->db->last_query()); echo '</pre>';
    //echo '<pre>'; var_dump($data['repo_sales']); echo '</pre>'; die();
    $this->template('repo/inventory.php', $data);
  }

  public function in() {
    $this->access(17);
    $this->header_data('title', 'Repo Inventory');
    $this->header_data('nav', 'repo-inventory');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?v1.0.0"></script>');
    $this->template('repo/in.php', []);
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

      $repo_engines = [];
      if ($this->session->flashdata('repo')) {
        $repo_engines = $this->session->flashdata('repo');
      }
      $output = [];
      if (empty($sales)) {
        $output['error'] = 'Engine number not found.';
      } elseif($sales['bcode'] === $_SESSION['branch_code']) {
        $output['error'] = 'You already claimed the engine.';
      } else {
        $url = base_url().'repo/claim';
        $registration_date = $sales['registration_date'];
        $date = new DateTime($registration_date);
        $now = $date->modify("+1 year");

        $form = <<<HTML
          <form class="form-inline span6 offset3" method="post" action="{$url}">
            <fieldset>
              <legend>Title Here</legend>
                <div class="row">
                  <div class="control-group offset1">
                    <label for="get-cust">Customer Code</label>
                    <input id="get-cust" type="text" value="{$sales['cust_code']}" disabled>
                    <!-- <input id="customer-id" type="hidden" name="cid" type="text" value=""> -->
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
                  <div class="control-group error">
                    <label class="control-label" for="date-regn">Date Registered</label>
                    <div class="controls">
                      <input id="date-regn" class="datepicker" type="text" name="regn-date" value="{$sales['registration_date']}" autocomplete="off">
                      <span class="help-inline">{$date->diff($now)->format('%d days')}</span>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <button type="submit" class="btn btn-success offset1">Save</button>
                </div>
            </fieldset>
          </form>
HTML;
        $output['form'] = $form;
      }
      $this->session->set_flashdata(['repo' =>  $repo_engines]);
      //$output['log'] = $sales;
      //$interval = date_diff('2020-01-01', date('Y-m-d'));
      $output['log'] = $sales;
      echo json_encode($output);
    }
  }

  public function claim_repo() {
    $this->access(17);
    if ($this->input->post('eid') && $this->input->post('cid')) {
      $return = $this->repo->claim($this->input->post('eid'), $this->input->post('cid'));
      $this->repo->insert_history($this->input->post('eid'));
      echo $return;
    }
  }

  public function customer() {
    $this->access(17);
    if ($this->input->post('cust_code')) {
      $customer = $this->sales->get_customer($this->input->post());
      echo json_encode($customer);
    }
  }

}


