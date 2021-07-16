<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Request_model extends CI_Model
{

  function __construct()
  {
    parent::__construct();
    $this->load->model('Message_model', 'message');
    if (!$this->session->has_userdata('username')) {
      show_404();
    }
  }
  function get_slug($company_id)
  {
    switch ($company_id) {
      case 1:
        return 'B1';
        break;
      case 3:
        return 'B2';
        break;
      case 6:
        return 'B3';
        break;
      case 8:
        return 'B4';
        break;
    }
  }
  function get_region_slug($id)
  {
    switch ($id) {
      case 1:
        return 'NCR';
        break;
      case 2:
        return 'R1';
        break;
      case 3:
        return 'R2';
        break;
      case 4:
        return 'R3';
        break;
      case 5:
        return 'R4';
        break;
      case 6:
        return 'R4b';
        break;
      case 7:
        return 'R5';
        break;
      case 8:
        return 'R6';
        break;
      case 9:
        return 'R7';
        break;
      case 10:
        return 'R8';
        break;
      case 11:
        return 'IX';
        break;
      case 12:
        return 'X';
        break;
      case 13:
        return 'XI';
        break;
      case 14:
        return 'XII';
        break;
      case 15:
        return 'XIII';
        break;
    }
  }
  function test_table(){
    $obj = new stdClass();
    $limit = $where = "";
    
    if (!empty($_POST['search'])) {
      $where = " WHERE CONCAT_WS(sid,bname,registration_type,payment_method,si_no) LIKE '%{$_POST['search']}%'";
    }

    $obj->count = $this->db->query("SELECT COUNT(IFNULL(sid,0)) as 'count' from tbl_sales {$where} limit 1")->row()->count;
    $obj->page = isset($_POST['page']) && is_numeric($_POST['page']) ? $_POST['page'] : 1;
    
    $obj->num_results_on_page = isset($_POST['pagination']) && is_numeric($_POST['pagination']) ? $_POST['pagination'] : 5;
    $obj->calc_page = ($obj->page - 1) * $obj->num_results_on_page;
    $limit = "LIMIT {$obj->calc_page},{$obj->num_results_on_page}";
    
    if (isset($_POST['pagination']) && $_POST['pagination'] == 0) {
      $obj->num_results_on_page = $obj->count;
      $obj->calc_page           = $obj->count;
  
    }

    $sort = (isset($_POST['sort']) && !empty($_POST['sort'])) ? "ORDER BY ".$_POST['sort'] : "";
    

    if (empty($_POST['is_init'])) {
      $_SESSION['ids'] = array();
    }

    if (isset($_POST['ids'])) {
      $_SESSION['ids'] =  array_unique (array_merge ($_SESSION['ids'], $_POST['ids'])); 
    }
    $obj->table = $this->db->query("SELECT * from tbl_sales {$where} {$sort} {$limit}")->result_array();
    return $obj;
  }
  function get_plate($id){
    return $this->db->query("SELECT 
    x.plate_number,
    x.date_encoded,
    x.received_dt,
    x.received_cust,
    x.plate_trans_no,
    UPPER(CONCAT(y.bcode,y.bname)) as branch,
    y.si_no,
    y.ar_no,
    UPPER(CONCAT(IFNULL(cus.last_name, ''),', ', IFNULL(cus.first_name, ''), LEFT(IFNULL(cus.middle_name,''),1))) as customer_name,
    s.status_name,
    cmp.company_code
     FROM tbl_plate x 
    inner join tbl_sales y on x.sid = y.sid
    inner join tbl_status s on s.status_id = x.status_id and s.status_type='PLATE'
    left join tbl_customer cus on  y.customer = cus.cid
    inner join tbl_company cmp on y.company = cmp.cid where x.plate_number = '{$id}' limit 1")->row();
  }
  function get_repo_branch_tip($branch = null)
  {
    if (empty($branch)) {
      return false;
    }
    return $this->db->query("SELECT x.*,concat(y.b_code,' ',y.name) as display from tbl_repo_branch_budget x inner join portal_global_2.tbl_branches y on x.bcode = y.b_code where x.bcode ={$branch} limit 1 ")->row();
  }
  function update_branch_tip()
  {
    $post = $this->input->post();
    $branch = $post['branch'];
    if (empty($branch)) {
      return $this->message->error('No Branch Code Selected!');
    }
    $this->db->trans_start();
    $data = array(
      'sop_renewal'                       => $post['renewal'],
      'sop_transfer'                      => $post['transfer'],
      'sop_hpg_pnp_clearance'             => $post['hpg_pnp_clearance'],
      'insurance'                         => $post['insurance'],
      'emission'                          => $post['emission'],
      'unreceipted_renewal_tip'           => $post['un_renewal'],
      'unreceipted_transfer_tip'          => $post['un_transfer'],
      'unreceipted_macro_etching_tip'     => $post['un_macro'],
      'unreceipted_hpg_pnp_clearance_tip' => $post['un_hpg_pnp_clearance'],
      'unreceipted_plate_tip'             => $post['un_plate']
    );
    $this->db->where('bcode', $branch);
    $this->db->update('tbl_repo_branch_budget', $data);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
    } else {
      $this->db->trans_commit();
      return $this->message->success("Branch Code {$branch} Updated!");
    }
  }
  function save_repo_branch_tip()
  {
    $post = $this->input->post();
    $branch = $post['branch'];
    $branchExists = $this->db->query("SELECT IFNULL(bcode,0) as result from tbl_repo_branch_budget where bcode = {$branch}");
    if (!empty($branchExists->row()->result)) {
      return $this->message->error('Branch Code ' . $branch . ', Already Exists!');
    }
    $this->db->trans_start();
    $data = array(
      'bcode'                             => $post['branch'],
      'sop_renewal'                       => $post['renewal'],
      'sop_transfer'                      => $post['transfer'],
      'sop_hpg_pnp_clearance'             => $post['hpg_pnp_clearance'],
      'insurance'                         => $post['insurance'],
      'emission'                          => $post['emission'],
      'unreceipted_renewal_tip'           => $post['un_renewal'],
      'unreceipted_transfer_tip'          => $post['un_transfer'],
      'unreceipted_macro_etching_tip'     => $post['un_macro'],
      'unreceipted_hpg_pnp_clearance_tip' => $post['un_hpg_pnp_clearance'],
      'unreceipted_plate_tip'             => $post['un_plate'],
      'region_id'                         => $_SESSION['region_id']
    );
    $this->db->insert('tbl_repo_branch_budget', $data);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
    } else {
      $this->db->trans_commit();
      return $this->message->success("Branch Code {$branch} Registered!");
    }
  }
  function get_repo_branch_tip_not_exists()
  {
    $region = $_SESSION['region_id'];
    $res = $this->db->query("SELECT IFNULL(group_concat(bcode),0) as 'bcodes' from tbl_repo_branch_budget where region_id = {$region}")->row();
    if (!empty($res->bcodes)) { // y.code
      return $this->db->query("SELECT x.b_code as value,concat(x.b_code,' ',x.name) as display from  portal_global_2.tbl_branches x inner join portal_global_2.tbl_companies y on y.cid = x.company where x.b_code not in (" . $res->bcodes . ") AND rrt_region_id = {$region}")->result_array();
    }
    return $this->db->query("SELECT x.b_code as value,concat(x.b_code,' ',x.name) as display from portal_global_2.tbl_branches  x inner join portal_global_2.tbl_companies y on y.cid = x.company where x.rrt_region_id = {$region}")->result_array();
  }

  function view_repo_matrix_table()
  {
    $region = $_SESSION['region_id'];
    // where x.region_id = {$region}
    return $this->db->query("SELECT x.*,y.name from tbl_repo_branch_budget x inner join portal_global_2.tbl_branches y on x.bcode = y.b_code where x.region_id = {$region}")->result_array();
  }
  function get_registration_sql($alias)
  {
    return "SUM(IFNULL(`$alias`.orcr_amt,0)+IFNULL(`$alias`.renewal_amt,0)+IFNULL(`$alias`.transfer_amt,0)+IFNULL(`$alias`.hpg_pnp_clearance_amt,0)+IFNULL(`$alias`.insurance_amt,0)+IFNULL(`$alias`.emission_amt,0)+IFNULL(`$alias`.macro_etching_amt,0)+IFNULL(`$alias`.renewal_tip,0)+IFNULL(`$alias`.transfer_tip,0)+IFNULL(`$alias`.hpg_pnp_clearance_tip,0)+IFNULL(`$alias`.macro_etching_tip,0))";
  }
  function get_liquidated($id)
  {
    return (intval($this->get_liquidated_misc($id)) + intval($this->get_liquidated_sales($id)));
  }
  function get_checked($id)
  {
    return (intval($this->get_checked_misc($id)) + intval($this->get_checked_sales($id)));
  }
  function get_liquidated_misc($id)
  {
    $res = $this->db->query("SELECT SUM(amount) as res from tbl_repo_misc where ca_ref = '{$id}' and status_id = 4 group by ca_ref")->row();
    return !empty($res->res) ? intval($res->res) : 0;
  }
  function get_liquidated_sales($id)
  {
    if (!empty($id)) {
      $res = $this->db->query("SELECT group_concat(x.repo_registration_id) as ids from tbl_repo_sales x left join tbl_repo_sap_upload_sales_batch y on x.repo_sales_id = y.repo_sales_id where x.repo_batch_id ={$id} AND x.status_id = 4")->row();
      if (!empty($res->ids)) {
        $subreturn = $this->db->query("SELECT SUM(orcr_amt+renewal_amt+transfer_amt+hpg_pnp_clearance_amt+insurance_amt+emission_amt+macro_etching_amt+renewal_tip+transfer_tip+hpg_pnp_clearance_tip+macro_etching_tip+plate_tip)  as res FROM `rms_db`.`tbl_repo_registration` where  repo_registration_id in ({$res->ids}) ")->row();
        return !empty($subreturn->res) ? intval($subreturn->res) : 0;
      }
    } else {
      return 0;
    }
  }

  function get_checked_misc($id)
  {
    $res = $this->db->query("SELECT SUM(amount) as res from tbl_repo_misc where ca_ref = '{$id}' and status_id = 3 group by ca_ref")->row();
    return !empty($res->res) ? intval($res->res) : 0;
  }

  function get_checked_sales($id)
  {
    if (!empty($id)) {
      $res = $this->db->query("SELECT group_concat(x.repo_registration_id) as ids from tbl_repo_sales x left join tbl_repo_sap_upload_sales_batch y on x.repo_sales_id = y.repo_sales_id where x.repo_batch_id ={$id} AND y.repo_sales_id IS NOT NULL AND x.status_id = 3")->row();
      if (!empty($res->ids)) {
        $subreturn = $this->db->query("SELECT SUM(orcr_amt+renewal_amt+transfer_amt+hpg_pnp_clearance_amt+insurance_amt+emission_amt+macro_etching_amt+renewal_tip+transfer_tip+hpg_pnp_clearance_tip+macro_etching_tip+plate_tip)  as res FROM `rms_db`.`tbl_repo_registration` where  repo_registration_id in ({$res->ids})")->row();
        return !empty($subreturn->res) ? intval($subreturn->res) : 0;
      }
    } else {
      return 0;
    }
  }

  function get_post_ids($array = array())
  {
    return $ids = implode(',', $array);
  }

  function get_misc_array()
  {
    $post = $this->input->post();
    if (isset($post['misc'])) {
      $ids = implode(',', $post['misc']);
      return $this->db->query("SELECT a.*,DATE(a.or_date) as or_date,b.status_name from tbl_repo_misc a inner join tbl_status b on a.status_id = b.status_id and b.status_type= 'MISC_EXP' where b.status_id NOT IN(90,1,0,3) AND a.mid in ({$ids}) group by a.mid ")->result_array();
    } else {
      return null;
    }
  }
  function get_sales_array()
  {
    $post = $this->input->post();
    $id = $this->input->post('repo_batch_id');
    if (isset($post['sales'])) {
      $ids = implode(',', $post['sales']);
      return $this->db->query("SELECT x.*,y.engine_no,z.status_name,{$this->get_registration_sql("b")} as sales_amt  from 
      tbl_repo_sales x inner join 
      tbl_engine y on x.engine_id = y.eid inner join 
      tbl_status z on x.status_id = z.status_id and z.status_type= 'REPO_SALES' left join 
      tbl_repo_registration b on x.repo_registration_id = b.repo_registration_id  left join 
      tbl_repo_sap_upload_sales_batch susb ON x.repo_sales_id = susb.repo_sales_id
                WHERE  susb.repo_sales_id IS NULL AND x.repo_batch_id = {$id} AND x.da_id IN (0,3) AND x.status_id = 3 AND x.repo_sales_id in ({$ids}) group by x.repo_sales_id")->result_array();
    } else {
      return null;
    }
  }

  function get_batch_misc($id)
  {
    return $this->db->query("SELECT a.*,DATE(a.or_date) as or_date,b.status_name from tbl_repo_misc a inner join tbl_status b on a.status_id = b.status_id and b.status_type= 'MISC_EXP' where b.status_id NOT IN(90,1,0,3) AND a.ca_ref ='{$id}' group by a.mid")->result_array();
  }

  function get_batch_sales($id)
  {
    return $this->db->query("SELECT x.*,y.engine_no,z.status_name,{$this->get_registration_sql("b")} as sales_amt  from 
    tbl_repo_sales x inner join 
    tbl_engine y on x.engine_id = y.eid inner join 
    tbl_status z on x.status_id = z.status_id and z.status_type= 'REPO_SALES' left join 
    tbl_repo_registration b on x.repo_registration_id = b.repo_registration_id  left join 
    tbl_repo_sap_upload_sales_batch susb ON x.repo_sales_id = susb.repo_sales_id
              WHERE  susb.repo_sales_id IS NULL AND x.repo_batch_id = {$id} AND x.da_id IN (0,3) AND x.status_id = 3 group by x.repo_sales_id")->result_array();
    //I think kelagan tangalin yung status id = 3
  }

  function sales_disapprove_status()
  {
    return $this->db->query("SELECT status_id as `id`, UPPER(status_name) as `value` from tbl_status where status_type ='REPO_DA' and status_id in (1,2) ")->result_array();
  }

  function get_batch($id)
  {
    return $this->db->query("SELECT * FROM tbl_repo_batch x where x.repo_batch_id = {$id} limit 1")->row();
  }
  function get_return_fund($id)
  {
    return $this->db->query("SELECT * FROM tbl_repo_return_fund x where x.id = '{$id}' limit 1")->row();
  }
  function view_repo_misc()
  {
    $id = $this->input->post('misc_id');
    return $this->db->query("SELECT x.*,DATE_FORMAT(x.date, '%Y-%m-%d') as dt,y.reference,z.status_name as da_reason,UPPER(zz.status_name) as misc_status from tbl_repo_misc x inner join tbl_repo_batch y on x.ca_ref = y.repo_batch_id left join tbl_status z on x.da_status_id = z.status_id  and z.status_type = 'MISC_DA_REASON' inner join tbl_status zz on x.status_id = zz.status_id  and zz.status_type = 'MISC_EXP' where x.mid = $id limit 1")->row();
  }

  function view_repo_sale()
  {
    $id = $this->input->post('repo_sale_id');
    return $this->db->query("SELECT z.reference,x.*,y.* FROM rms_db.tbl_repo_sales x left join tbl_repo_registration y on x.repo_registration_id = y.repo_registration_id inner join tbl_repo_batch z on x.repo_batch_id = z.repo_batch_id where  x.repo_sales_id = $id limit 1")->row();
  }


  function repo_fund_change_status()
  {
    return $this->db->query("SELECT status_id,UPPER(status_name) as status_name from tbl_status x where status_type = 'RETURN_FUND' and status_id NOT IN (1,2,90)")->result_array();
  }

  function repo_misc_change_status()
  {
    return $this->db->query("SELECT status_id as id,UPPER(status_name) as `value` from tbl_status x where status_type = 'MISC_DA_REASON'")->result_array();
  }

  function expense_type()
  {
    return $this->db->query("SELECT `type` from tbl_misc_type")->result_array();
  }

  function batch_dropdown()
  {
    return $this->db->query("SELECT repo_batch_id as `value`,reference as display from tbl_repo_batch where bcode = {$_SESSION['branch_code']}")->result_array();
  }

  function upload_file($input_name, $dir, $column, $suffix = '')
  {
    if (!empty($_FILES[$input_name]['size'])) {
      $post = $this->input->post();
      $file = md5($_SESSION['branch_code'] . date('Y-m-d H:m:s')) . $suffix . '.jpg';
      $location = $dir . '/';
      if (!is_dir(FCPATH . $location)) {
        mkdir(FCPATH . $location, 0775, true);
      }
      move_uploaded_file($_FILES[$input_name]['tmp_name'], FCPATH . $location . $file);
      return array($column => $location . $file);
    } else {
      return array();
    }
  }
  function reject_repo_misc()
  {
    $post = $this->input->post();
    if ($post['edit_id']) {
      $this->db->trans_start();
      $this->db->where('mid', $post['edit_id']);
      $this->db->update('tbl_repo_misc', $data = array("da_status_id" => $post['new_status'], "status_id" => 5));
      $data = array(
        'mid'     => $post['edit_id'],
        'status'  => 5,
        'uid'     => $_SESSION['uid']
      );
      $this->db->insert('tbl_repo_misc_expense_history', $data);
      $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
      } else {
        $this->db->trans_commit();
        return $this->message->success('Miscellaneous Disapproved!');
      }
    }
  }
  function change_status_repo_return_fund()
  {
    $post = $this->input->post();
    $res  = $this->get_return_fund($post['return_fund_id']);
    $sub_data = array("status_id" => $post['change_status']);
    if ($post['change_status'] == 30) {
      $sub_data += array("liq_date" => date('Y-d-m H:i:s'));
    }
    $this->db->trans_start();
    $this->db->where('id', $post['return_fund_id']);
    $this->db->update('tbl_repo_return_fund',  $sub_data);
    $data = array(
      'status_id'       => $post['change_status'],
      'return_fund_id'  => $post['return_fund_id'],
      'repo_batch_id'   => $res->repo_batch_id,
      'created_by'      => $_SESSION['uid']
    );
    $this->db->insert('tbl_repo_return_fund_history', $data);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
    } else {
      $this->db->trans_commit();
      return $this->message->success('Repo Return Fund Changed Status!');
    }
  }
  function update_repo_return_fund()
  {
    $post = $this->input->post();
    $res = $this->get_return_fund($post['return_fund_id']);
    $new_status = 1;
    $this->db->trans_start();
    $data = array();
    switch ($res->status_id) {
      case 3:
        $data += array("amount" => $post['amount']);
        break;
      case 4:
        $data += array("is_deleted" => 1);
        $new_status = 90;
        break;
      case 5:
        $data += $this->upload_file('attachment', '/rms_dir/repo/batch/return_fund/' . $res->repo_batch_id, 'image_path');
        break;
    }
    $data += array("status_id" => $new_status);
    $this->db->where('id', $post['return_fund_id']);
    $this->db->update('tbl_repo_return_fund', $data);
    $data = array(
      'status_id'       => $new_status,
      'return_fund_id'  => $post['return_fund_id'],
      'repo_batch_id'   => $res->repo_batch_id,
      'created_by'      => $_SESSION['uid']
    );
    $this->db->insert('tbl_repo_return_fund_history', $data);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
    } else {
      $this->db->trans_commit();
      return $this->message->success('Repo Return Fund Updated!');
    }
  }
  function submit_repo_sale()
  {
    $post = $this->input->post();
    if (isset($_POST['ids'])) {
      $this->db->trans_start();
      foreach ($post['ids'] as $res) {
        $this->db->where('repo_sales_id', $res);
        $this->db->update('tbl_repo_sales', array("da_id" => 0));
        $data = array(
          'repo_sales_id' => $res,
          'status_id'     => 0,
          'uid'           => $_SESSION['uid']
        );
        $this->db->insert('tbl_repo_da_history', $data);
      }
      $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
      } else {
        $this->db->trans_commit();
        return $this->message->success('Repo Sales Submitted!');
      }
    } else {
      return $this->message->error('No Repo Sales Selected!');
    }
  }
  function reject_repo_sale()
  {
    $post = $this->input->post();
    $this->db->trans_start();
    $this->db->where('repo_registration_id', $post['edit_id']);
    $this->db->update('tbl_repo_sales', array("da_id" => $post['new_status']));

    $data = array(
      'repo_sales_id' => $post['edit_id'],
      'status_id'     => $post['new_status'],
      'uid'           => $_SESSION['uid']
    );
    $this->db->insert('tbl_repo_da_history', $data);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
    } else {
      $this->db->trans_commit();
      return $this->message->success('Repo Sales Rejected!');
    }
  }
  function update_repo_sale()
  {
    $post = $this->input->post();
    $da   = $this->db->query("SELECT da_id,repo_sales_id from tbl_repo_sales where repo_registration_id = '" . $post['edit_id'] . "'  limit 1")->row();
    $da_id = !empty($da->da_id) ? $da->da_id : '';
    $this->db->trans_start();
    $data = array();
    #Wrong Amount
    if ($da_id == 1) {
      $data += array(
        "orcr_amt"              => $post['orcr_amt'],
        "renewal_amt"           => $post['trans_amt'],
        "transfer_amt"          => $post['ins_amt'],
        "hpg_pnp_clearance_amt" => $post['macro_amt'],
        "insurance_amt"         => $post['re_amt'],
        "emission_amt"          => $post['pnp_amt'],
        "macro_etching_amt"     => $post['em_amt']
      );
    }
    #Wrong Attachment
    if ($da_id == 2) {
      $data += $this->upload_file('reg_img', '/rms_dir/repo/registration/' . $post['edit_id'], 'att_reg_orcr', 'registration');
      $data += $this->upload_file('ren_img', '/rms_dir/repo/registration/' . $post['edit_id'], 'att_renew_or', 'renewal');
      $data += $this->upload_file('reg_trans', '/rms_dir/repo/registration/' . $post['edit_id'], 'att_trans_or', 'transfer');
      $data += $this->upload_file('reg_pnp', '/rms_dir/repo/registration/' . $post['edit_id'], 'att_pnp_or', 'php');
      $data += $this->upload_file('reg_ins', '/rms_dir/repo/registration/' . $post['edit_id'], 'att_ins_or', 'insurance');
      $data += $this->upload_file('reg_em', '/rms_dir/repo/registration/' . $post['edit_id'], 'att_em_or', 'emission');
      $data += $this->upload_file('reg_mac', '/rms_dir/repo/registration/' . $post['edit_id'], 'att_macro_e_or', 'macro');
    }
    $this->db->where('repo_registration_id', $post['edit_id']);
    $this->db->update('tbl_repo_registration', $data);

    $data = array("da_id" => 3);
    $this->db->where('repo_registration_id', $post['edit_id']);
    $this->db->update('tbl_repo_sales', $data);

    $data = array(
      'repo_sales_id' => $da->repo_sales_id,
      'status_id'     => 3,
      'uid'           => $_SESSION['uid']
    );
    $this->db->insert('tbl_repo_da_history', $data);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
    } else {
      $this->db->trans_commit();
      switch ($da_id) {
        case 1:
          return $this->message->success('Repo Sales Wrong Amount Resolved!');
          break;
        case 2:
          return $this->message->success('Repo Sales Missing/Unreadable Attachment Resolved!');
          break;
      }
    }
  }
  function update_repo_misc()
  {
    $post = $this->input->post();
    if ($post['edit_id']) {

      $data = array();
      $location = null;
      $this->db->trans_start();
      $data += $this->upload_file('file', '/rms_dir/repo/batch/misc_exp/' . $post['batch_no'], 'image_path');
      $data += array(
        "ca_ref"    => $post['batch_no'],
        "date"      => $post['date'],
        "or_no"     => $post['or_no'],
        "type"      => $post['expense_type'],
        "amount"    => $post['amount'],
        "status_id" => 3,
      );
      $this->db->where('mid', $post['edit_id']);
      $this->db->update('tbl_repo_misc', $data);
      $data = array(
        'mid'     => $post['edit_id'],
        'status'  => 3,
        'uid'     => $_SESSION['uid']
      );
      $this->db->insert('tbl_repo_misc_expense_history', $data);
      $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
      } else {
        $this->db->trans_commit();
        return $this->message->success('Miscellaneous Updated!');
      }
    }
  }

  function insert_repo_return_fund()
  {
    $post = $this->input->post();
    $data = array();
    $this->db->trans_start();
    $data += $this->upload_file('attachment', '/rms_dir/repo/batch/return_fund/' . $post['batch_id'], 'image_path');
    $data += array(
      'repo_batch_id'  => $post['batch_id'],
      'amount'         => $post['amount'],
      'status_id'      => 1
    );
    $this->db->insert('tbl_repo_return_fund', $data);
    $id = $this->db->insert_id();
    $data = array(
      'status_id'       => 1,
      'return_fund_id'  => $id,
      'repo_batch_id'  => $post['batch_id'],
      'created_by'      => $_SESSION['uid']
    );
    $this->db->insert('tbl_repo_return_fund_history', $data);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
    } else {
      $this->db->trans_commit();
      return $this->message->success('Return Fund Added!');
    }
  }
  function save_for_checking()
  {
    $post = $this->input->post();
    $repo_batch = $this->get_batch($post['repo_batch_id']);
    $slug  = $this->get_slug($repo_batch->company_id);
    $rslug = $this->get_region_slug($repo_batch->region_id);
    $this->db->trans_start();
    $batch_name = 'T-' . $rslug . '-' . date('ymd') . '-' . $slug;
    $batch = $this->db->query("SELECT a.*, b.batch_count FROM tbl_repo_sap_upload_batch a INNER JOIN ( SELECT  MAX(subid) AS subid, COUNT(*) AS batch_count FROM tbl_repo_sap_upload_batch WHERE trans_no LIKE '" . $batch_name . "%'
      ) b ON a.subid = b.subid")->row_array();

    if ($batch['is_uploaded'] === "0") {
      $subid = $batch['subid'];
    } else {
      $data['subid'] = NULL;

      if ($batch === NULL) {
        $data['trans_no'] =  $batch_name . '-1';
      } elseif ($batch['is_uploaded'] === "1") {
        $new_batch_count = $batch['batch_count'] + 1;
        $data['trans_no'] = $batch_name . '-' . $new_batch_count;
      }
      $this->db->insert('tbl_repo_sap_upload_batch', $data);
      $subid = $this->db->insert_id();
    }
    # INSERT SALE
    if (!empty($post['sales'])) {
      foreach (explode(',', $post['sales']) as $sale) {
        $sales_batch = array(
          'subid' => $subid,
          'repo_sales_id' => $sale
        );
        $this->db->insert('tbl_repo_sap_upload_sales_batch', $sales_batch);
      }
    }
    # INSERT MISC
    if (!empty($post['misc'])) {
      foreach (explode(',', $post['misc']) as $misc) {
        $this->db->query(
          "
          INSERT INTO
            tbl_repo_misc_expense_history(id, mid, remarks, status, uid)
          VALUES
            (NULL, " . $misc . ", NULL, 3, " . $_SESSION['uid'] . ")"
        );
        $this->db->where('mid', $misc);
        $this->db->update('tbl_repo_misc', array("status_id" => 3));
      }
    }
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return $this->message->error('Something Went Wrong Call Your Administrator For Assistance!');
    } else {
      $this->db->trans_commit();
      return $this->message->success('Created ' . $batch_name . '!');
    }
  }
}
