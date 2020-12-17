<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repo extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Sales_model', 'sales');
    $this->load->model('Repo_model', 'repo');
    $this->load->model('File_model', 'file');
  }

  private $jsversion = 'v1.0.0';

  public function index() {

    $this->access(17);
    $this->header_data('title', 'Repo Inventory');
    $this->header_data('nav', 'repo-inventory');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');

    $repo_inventory = [];
    $inventory = $this->repo->inventory();
    foreach ($inventory as $repo) {
      $expiry = $this->repo->expiration($repo['date_registered']);
      $repo['status'] = $expiry['status'];
      $repo['message'] = $expiry['message'];
      $repo_inventory[] = $repo;
    }
    $data['repo_inventory'] = $repo_inventory;
    $this->template('repo/inventory', $data);
  }

  public function in() {
    $this->access(17);
    $this->header_data('title', 'Repo In');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');
    $this->template('repo/in', []);
  }

  public function sale($repo_inventory_id) {
    $this->access(17);

    if ($this->input->post('save')) {
      $validation = [
        //REPO SALE
        [ 'field' => 'repo_sale[rsf_num]', 'label' => 'RSF#', 'rules' => 'required' ],
        [ 'field' => 'repo_sale[ar_num]', 'label' => 'AR Number', 'rules' => 'required' ],
        [ 'field' => 'repo_sale[ar_amt]', 'label' => 'Amount Given', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_sale[date_sold]', 'label' => 'Date Sold', 'rules' => 'required' ],
        //CUSTOMER
        [ 'field' => 'customer[cust_code]', 'label' => 'Customer Code', 'rules' => 'required' ],
        [ 'field' => 'customer[first_name]', 'label' => 'First Name', 'rules' => 'required' ],
        [ 'field' => 'customer[last_name]', 'label' => 'Last Name', 'rules' => 'required' ],
      ];
      $this->form_validation->set_rules($validation);

      if ($this->form_validation->run()) {
        $this->db->trans_start();
        $sales = $this->input->post();
        $repo_batch_id = $this->repo->generate_batch();

        $sales['repo_sale']['repo_batch_id'] = $repo_batch_id;
        $sales['repo_sale']['repo_inventory_id'] = $repo_inventory_id;
        $this->repo->save_sales($sales);
        $this->repo->initialize_regn([
          'repo_inventory_id' => $repo_inventory_id,
          'repo_batch_id' => $repo_batch_id
        ]);
        $this->repo->update_inv_status($repo_inventory_id, 'SALES');
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
          redirect($_SERVER['HTTP_REFERER']);
        }
        // Todo Add success message Success!!! (Engine #) Sold.
        $_SESSION['messages'][] = 'Success!!!';
        redirect('repo/inventory');
      }
    }

    $data['repo'] = $this->repo->engine_details($repo_inventory_id, 'NEW');
    $this->check_mc_branch($data['repo']['bcode']);
    $date = (!$this->input->post('save')) ? $data['repo']['date_registered'] : $this->input->post('repo_registration')['date_registered'];
    $expire = $this->repo->expiration($date);

    $data['repo']['expire_status'] = $expire['status'];
    $data['repo']['expire_message'] = $expire['message'];
    $data['disable'] = 'disabled';

    $this->header_data('title', 'Repo Sales');
    $this->header_data('nav', 'repo-sales');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');
    $this->template('repo/sales', $data);
  }

  public function registration($repo_inventory_id) {
    $this->access(17);
    $this->header_data('title', 'Repo Registration');
    $this->header_data('nav', 'repo-registration');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');

    if ($this->input->post('save')) {
      $validation = [
        //REGISTRATION
        [ 'field' => 'repo_registration[registration_amt]', 'label' => 'Registration Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_registration[pnp_clearance_amt]', 'label' => 'PNP Clearance Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_registration[macro_etching_amt]', 'label' => 'Macro Etching', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_registration[insurance_amt]', 'label' => 'Insurance Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_registration[emission_amt]', 'label' => 'Emission Amount', 'rules' => 'required|numeric|greater_than_equal_to[0]' ],
        [ 'field' => 'repo_registration[date_registered]', 'label' => 'Registration Date', 'rules' => 'required' ],
      ];

      $tip = $this->repo->get_tip_matrix($_SESSION['rrt_region_id']);
      if (isset($tip)) {
        $validation[] =  [ 'field' => 'repo_registration[or_tip]', 'label' => 'OR Tip', 'rules' => 'required|numeric|less_than_equal_to['.$tip['repo_or'].']' ];
        $validation[] =  [ 'field' => 'repo_registration[pnp_tip]', 'label' => 'PNP Tip', 'rules' => 'required|numeric|less_than_equal_to['.$tip['repo_or'].']' ];
      }

      $this->form_validation->set_rules($validation);
      if ($this->form_validation->run()) {
        $repo_registration_id = $this->repo->save_registration(
          $repo_inventory_id,
          $this->input->post('repo_registration')
        );
        $upload_attachments = $this->file->upload('attachments', '/repo/registration/'.$repo_registration_id);
        redirect('repo/view/'.$repo_inventory_id);
      }
    }

    $data['repo'] = $this->repo->engine_details($repo_inventory_id, 'SALES');
    $date = (!$this->input->post('save')) ? $data['repo']['date_registered'] : $this->input->post('repo_registration')['date_registered'];
    $expire = $this->repo->expiration($date);

    $data['repo']['expire_status'] = $expire['status'];
    $data['repo']['expire_message'] = $expire['message'];
    $data['disable'] = 'disabled';

    $this->template('repo/registration', $data);
  }

  public function view($repo_inventory_id = NULL) {
    if (!isset($repo_inventory_id)) {
      show_404();
    }
    $this->access(17);

    $data['repo'] = $this->repo->engine_details($repo_inventory_id, NULL);
    $this->check_mc_branch($data['repo']['bcode']);

    $data['histories'] = $this->repo->get_history($repo_inventory_id);
    if (isset($data['repo']['attachment'])) {
      $data['attachment'] = true;
      foreach (json_decode($data['repo']['attachment'], 1) as $key => $attach) {
        $data[$key] =  base_url($attach);
      }
    } else {
      $data['attachment'] = false;
    }

    $expire = $this->repo->expiration($data['repo']['date_registered']);
    $data['repo']['expire_status'] = $expire['status'];
    $data['repo']['expire_message'] = $expire['message'];

    $this->header_data('title', 'Repo View');
    $this->header_data('nav', 'repo-view');
    $this->template('repo/view.php', $data);
  }

  public function get_sales() {
    if($this->input->post('engine_no')) {
      $select_clause = <<<SQL
        e.*, s.sid, s.cr_no,
        DATE_FORMAT(IFNULL(rr.date_registered, s.cr_date), '%Y-%m-%d') AS date_registered,
        IFNULL(rsc.cid,sc.cid) AS customer_id,
        IFNULL(rsc.cust_code,sc.cust_code) AS cust_code,
        IFNULL(rsc.first_name,sc.first_name) AS first_name,
        IFNULL(rsc.last_name, sc.last_name) AS last_name,
        ,rs.repo_sales_id,
        IFNULL(ri.bcode,s.bcode) AS bcode,
        ri.bcode AS already_claimed,
        IFNULL(ri.bname, s.bname) AS bname,
        DATE_FORMAT(IFNULL(rs.date_sold, s.date_sold), '%Y-%m-%d') AS date_sold
SQL;
      $where_clause = <<<SQL
        e.engine_no = '{$this->input->post("engine_no")}'
        AND (s.sid IS NULL OR s.status >= 4)
        AND (`ri`.`status` IS NULL OR ri.status != 'Registered')
SQL;

      $sales = $this->repo->get_repo_in($select_clause, $where_clause);

      $output = [];
      if (empty($sales)) {
        $output['error'] = 'Engine number not found.';
      } elseif($sales['already_claimed'] === $_SESSION['branch_code']) {
        $output['error'] = 'You already claimed this engine.';
      } else {
        $expire = $this->repo->expiration($sales['date_registered']);
        $xpr_msg = ($expire['status'] === 'success') ? 'Expire in' : '';
        $form_open = form_open(base_url('repo/claim'), ["class" => "form-inline span6 offset3", "onsubmit" => "return confirm('Are you sure?');"]);
        $form = <<<HTML
          {$form_open}
            <input type="hidden" name="engine_id" value="{$sales['eid']}" >
            <input type="hidden" name="customer_id" value="{$sales['customer_id']}">
            <fieldset>
              <legend>Details</legend>
                <div class="row">
                  <div class="control-group offset1">
                    <label>Branch</label>
                    <input type="text" value="{$sales['bcode']} {$sales['bname']}" disabled>
                  </div>
                </div>
                <div class="row">
                  <div class="control-group offset1">
                    <label for="get-cust">Customer Code</label>
                    <input id="get-cust" type="text" value="{$sales['cust_code']}" disabled>
                    <!-- <input id="customer-id" type="hidden" name="cid" type="text" value="{$sales['customer_id']}"> -->
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
                      <input type="text" name="date_registered" value="{$sales['date_registered']}" disabled>
                      <span class="help-inline">{$xpr_msg} {$expire['message']}</span>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <button class="btn btn-success offset1" type="submit" name="repo-in" value="true">Repo In</button>
                </div>
            </fieldset>
          </form>
HTML;
        $output['form'] = $form;
      }
      $output['log'] = $this->session->flashdata('repo');
      echo json_encode($output);
    }
  }

  public function claim() {
    $this->access(17);
    if ($this->input->post('repo-in')) {
      $this->repo->claim($this->input->post('engine_id'));
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

  public function batch() {
    $this->access(17);
    $this->header_data('title', 'Repo Batch');
    $this->header_data('nav', 'repo-batch');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');
    $data['batches'] = $this->repo->batch_list();
    $this->template('repo/batch/list', $data);
  }

  public function batch_view($repo_batch_id) {
    $this->access(17);
    $this->header_data('title', 'Repo Batch CA View');
    $this->header_data('nav', 'repo-batch-ca-view');
    $data['batch'] = $this->repo->batch($repo_batch_id);
    $data['reference'] = $data['batch'][0]['reference'];
    $batch_misc = $data['batch'][0]['misc_expenses'];
    $data['batch_misc'] = (isset($batch_misc)) ? json_decode($batch_misc, 1) : NULL;

    $this->template('repo/batch/view', $data);
  }

  public function misc_exp() {
    $this->access(17);
    $this->header_data('title', 'Repo Registration Misc Expense');
    $this->header_data('nav', 'repo-registration-misc-expense');
    $data['batches'] = $this->repo->batch_list();
    $data['hidden'] = 'hidden';
    $data['disabled'] = 'disabled';

    if ($this->input->post('save')) {
      $expense_type = $this->input->post('expense_type');
      $validation = [
        [ 'field' => 'repo_batch_id', 'label' => 'Reference Number', 'rules' => 'required' ],
        [ 'field' => 'expense_type', 'label' => 'Expense Type', 'rules' => 'required' ],
        [ 'field' => 'amount', 'label' => 'Misc Expense Amount', 'rules' => 'required' ],
      ];

      if ($expense_type === 'Others') {
        $validation[] = [ 'field' => 'others', 'label' => 'Others', 'rules' => 'required' ];
      }

      $this->form_validation->set_rules($validation);
      if ($this->form_validation->run()) {
        $repo_batch_id = $this->input->post('repo_batch_id');
        $expense_id = md5(date('Y-m-d H:m:s'));
        $upload = $this->file->upload('misc', '/repo/batch/'.$repo_batch_id.'/', $expense_id.'.jpg');

        if ($upload) {
          $img_path = '/rms_dir/repo/batch/'.$repo_batch_id.'/'.$expense_id.'.jpg';
          $misc_saved = $this->repo->save_expense([
            "repo_batch_id" => $repo_batch_id,
            "data" => [
              $expense_id => [
                "expense_type" => $expense_type,
                "amount" => $this->input->post('amount'),
                "image_path" => $img_path,
                "status" => "For Checking",
                "is_deleted" => "0"
              ]
            ]
          ]);

          if ($misc_saved) {
            $_SESSION['messages'][] = 'Misc expense uploaded successfully.';
          } else {
            $_SESSION['warning'][] = 'Something went wrong.';
          }
        }
      }

      if (strlen(set_select('expense_type', 'Others')) > 0) {
        $data['hidden'] = '';
        $data['disabled'] = '';
      }
    }
    $this->template('repo/batch/misc', $data);
  }

  public function batch_print($repo_batch_id) {
    $data = $this->repo->print_batch($repo_batch_id);
    $this->load->view('repo/batch/print', $data);
  }

  public function get_expiration() {
    echo json_encode($this->repo->expiration($this->input->post('registration_date')));
  }

  public function ca() {
    $this->access(1); // Todo add page access
    if ($this->input->post()) {
      $repo_batch_ids = array_keys($this->input->post('batches'));
      $validation = [];
      foreach ($repo_batch_ids as $repo_batch_id) {
        $validation[] = [ 'field' => 'batches['.$repo_batch_id.']', 'label' => 'Document Number', 'rules' => 'required' ] ;
      }
      $this->form_validation->set_rules($validation);

      if ($this->form_validation->run()) {
        $success = $this->repo->save_ca($this->input->post('batches'));
        if ($success) {
          $_SESSION['messages'][] = 'CA Reference added Document Number successfully.';
        }
      }
    }
    $this->header_data('title', 'Repo CA');
    $this->header_data('nav', 'projected_fund');
    $data['for_cas'] = $this->repo->get_for_ca();
    $this->template('repo/acctg/ca', $data);
  }

  public function print_ca() {
    $this->access(1); // Todo add page access
    if (!$this->input->post('batches')) {
      show_404();
    }
    $data['region_company'] = $this->input->post('region_company');
    $data['prints'] = $this->repo->print_ca(array_keys($this->input->post('batches')));
    $this->template('repo/acctg/print_ca', $data);
  }
}
