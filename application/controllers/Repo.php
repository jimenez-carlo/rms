<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repo extends MY_Controller {

  private $jsversion = 'v1.0.0';

  public function __construct() {
    parent::__construct();
    $this->load->model('Sales_model', 'sales');
    $this->load->model('Repo_model', 'repo');
    $this->load->model('File_model', 'file');
    $this->load->model('Validation_model', 'validate');
    $this->load->model('Form_model', 'form');
  }

  public function index() {
    $this->access(17);
    $this->header_data('title', 'Repo Inventory');
    $this->header_data('nav', 'repo-inventory');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');
    $inventory = $this->repo->inventory();
    $data['inventory_table'] = $inventory;
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
      if ($this->validate->form('REPO_SALES')) {
        $this->db->trans_start();
        $sales = $this->input->post();
        $sales['repo_sale']['repo_inventory_id'] = $repo_inventory_id;
        $this->repo->save_sales($sales);
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
    //$this->check_mc_branch($data['repo']['bcode']);
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

  public function registration($repo_inventory_id, $repo_sales_id) {
    $this->access(17);
    $this->header_data('title', 'Repo Registration');
    $this->header_data('nav', 'repo-registration');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');

    $data = $this->repo->get_branch_tip_matrix($_SESSION['branch_code']);
    if ($save_registration = $this->input->post('save') && $repo_registration = $this->input->post('repo_registration')) {
      if ($this->validate->form('REPO_REGISTRATION', $data)) {
        $repo_registration_id = $this->repo->save_registration($repo_inventory_id, $repo_sales_id, $repo_registration);
        redirect('repo/view/'.$repo_inventory_id);
      }
    }

    $data['repo'] = $this->repo->engine_details($repo_inventory_id, 'SALES');
    $date = $this->input->post('date_registered') ?? $data['repo']['date_registered'];
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
    $this->template('repo/view', $data);
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

  public function create_ca() {
    $data['table_sales'] = $this->repo->request_ca();
    if ($repo_sales_id = $this->input->post('repo_sales_id')) {
      $success = $this->repo->generate_ca($repo_sales_id);
      if ($success['status']) {
        $_SESSION['messages'][] = $success['repo_ca_reference'].' created successfuly.';
        redirect('repo/ca_batch');
      }
    }
    $this->template('repo/batch/create', $data);
  }

  public function ca_batch() {
    $this->access(17);
    $this->header_data('title', 'Repo Batch');
    $this->header_data('nav', 'repo-batch');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');
    $data['table_batches'] = $this->repo->batch_list();
    $this->template('repo/batch/list', $data);
  }

  public function batch_view($repo_batch_id) {
    $this->access(17);
    $this->header_data('title', 'Repo Batch CA View');
    $this->header_data('nav', 'repo-batch-ca-view');
    $data = $this->repo->batch($repo_batch_id);
    $this->template('repo/batch/view', $data);
  }

  public function misc_exp() {
    $this->access(17);
    $this->header_data('title', 'Repo Registration Misc Expense');
    $this->header_data('nav', 'repo-registration');
    $this->footer_data('script', '<script src="'.base_url().'assets/js/repo_registration.js?'.$this->jsversion.'"></script>');

    $data['batchref_dropdown'] = form_dropdown("repo_batch_id", $this->form->ca_dropdown('REPO'), '', ["class"=>"span5"]);
    $data['hidden'] = 'hidden';
    $data['disabled'] = 'disabled';

    if ($this->input->post('save')) {
      $repo_batch_id = $this->input->post('repo_batch_id');
      $expense_type = $this->input->post('expense_type');
      $or_no = $this->input->post('or_no');
      $or_date = $this->input->post('or_date');
      $expense_id = md5($_SESSION['branch_code'].date('Y-m-d H:m:s'));

      $upload = $this->file->upload('misc', '/repo/batch/misc_exp/'.$repo_batch_id.'/', $expense_id.'.jpg');
      $form_ok = $this->validate->form('REPO_BATCH_MISC_EXP', [ 'expense_type' => $expense_type ]);
      if ($upload && $form_ok) {
        $img_path = '/rms_dir/repo/batch/misc_exp/'.$repo_batch_id.'/'.$expense_id.'.jpg';
        $misc_saved = $this->repo->save_expense([
          "repo_batch_id" => $repo_batch_id,
          "data" => [
            $expense_id => [
              "or_no" => $or_no,
              "expense_type" => $expense_type,
              "amount" => $this->input->post('amount'),
              "image_path" => $img_path,
              "status" => "FOR CHECKING",
              "or_date" => $or_date,
              "date_time_created" => date('Y-m-d H:m:s'),
              "is_deleted" => "0",
            ]
          ]
        ]);

        if ($misc_saved) {
          $_SESSION['messages'][] = 'Misc expense uploaded successfully.';
        } else {
          $_SESSION['warning'][] = 'Something went wrong.';
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
    if ($this->input->post("save_doc_no")) {
      $this->form_validation->set_rules('doc_no', 'Document #', 'trim|required|min_length[4]');
      if ($this->form_validation->run()) {
        $ca['repo_batch_id'] = $this->input->post('repo_batch_id');
        $ca['doc_no'] = $this->input->post('doc_no');
        $ca['status'] = "FOR DEPOSIT";
        $ca['date_doc_no_encoded'] = date('Y-m-d H:m:s');
        $isSaved = $this->repo_save($ca,"INPUT_DOC_NUMBER");
        if (!$isSaved) {
          $this->output->set_status_header(500);
          $status_msg = "Error in database.";
        } else {
          echo '{"message":"Document# '.$ca['doc_no'].' has been saved successfully!"}';
        }
      } else {
        $this->output->set_status_header(400);
        echo validation_errors();
      }
      exit;
    }

    $this->header_data('title', 'Repo CA');
    $this->header_data('nav', 'repo-registration');
    $data['input_region'] = form_dropdown("region", $this->form->region_dropdown('WITH_OUT_ANY'), 0, ["class"=>"span12"]);
    $data['for_ca'] = $this->repo->get_for_ca();
    $this->template('repo/acctg/ca', $data);
  }

  public function ca_template() {
    if ($this->input->post("download")) {
      $data = $this->repo->repo_ca_template();
      $this->load->view("projected_fund/ca_template", $data);
    }
  }

  public function print_ca_topsheet() {
    $this->access(1); // Todo add page access
    if (!$this->input->post('print')) {
      show_404();
    }
    $data = $this->repo->print_ca($this->input->post());
    $this->load->view('repo/acctg/print_ca', $data);
  }

  public function for_checking() {
    $this->access(18); // For Checking
    $repo_batch_id = $this->input->post('repo_batch_id');

    if ($repo_batch_id) {
      $reference_data =  $this->repo->check_registration($this->input->post('request_type'), ['repo_batch_id' => $repo_batch_id ]);
      echo $reference_data;
      exit;
    }

    if ($attachment = $this->input->post('attachment')) {
      $data_attached =  $this->repo->check_registration($this->input->post('request_type'), $attachment);
      echo $data_attached;
      exit;
    }

    if (!$this->input->post()) {
      $this->header_data('title', 'Repo Checking');
      $this->header_data('nav', 'repo-registration');
      $data['references'] = $this->repo->check_registration('GET_REFERENCE');
      $this->template('repo/for_checking', $data);
    }
  }

  private function repo_save(array $data, $type) {
    $this->db->trans_start();
    switch ($type) {
      case 'INPUT_DOC_NUMBER':
        $this->db->update('tbl_repo_batch', $data, "repo_batch_id=".$data['repo_batch_id']);
        break;
    }
    $this->db->trans_complete();
    return $this->db->trans_status();
  }
}
