<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Request_model extends CI_Model
{

  function __construct()
  {
    parent::__construct();
    if (!$this->session->has_userdata('username')) {
      show_404();
    }
  }

  function get_batch(){
    $id = $this->input->post('batch_id');
    return $this->db->query("SELECT * FROM tbl_repo_batch where repo_batch_id = {$id} limit 1")->row();
  }
  function get_return_fund($id){
    return $this->db->query("SELECT * FROM tbl_repo_return_fund  where id = '{$id}' limit 1")->row();
  }
  function view_repo_misc()
  {
    $id = $this->input->post('misc_id');
    return $this->db->query("SELECT x.*,DATE_FORMAT(x.date, '%Y-%m-%d') as dt from tbl_repo_misc x where x.mid = $id limit 1")->row();
  }
  
  function view_repo_sale()
  {
    $id = $this->input->post('repo_sale_id');
    return $this->db->query("SELECT z.reference,x.*,y.* FROM rms_db.tbl_repo_sales x left join tbl_repo_registration y on x.repo_registration_id = y.repo_registration_id inner join tbl_repo_batch z on x.repo_batch_id = z.repo_batch_id where  x.repo_sales_id = $id limit 1")->row();
  }

  function view_repo_return_fund()
  {
    $id = $this->input->post('return_fund_id');
    return $this->db->query("SELECT * from tbl_repo_return_fund x where x.id = {$id} limit 1")->row();
  }

  function expense_type()
  {
    return $this->db->query("SELECT `type` from tbl_misc_type")->result_array();
  }

  function batch_dropdown()
  {
    return $this->db->query("SELECT repo_batch_id as `value`,reference as display from tbl_repo_batch where bcode = {$_SESSION['branch_code']}")->result_array();
  }

  function upload_file($input_name, $dir, $column, $suffix=''){
    if (!empty($_FILES[$input_name]['size'])) {
    $post = $this->input->post();
    $file = md5($_SESSION['branch_code'] . date('Y-m-d H:m:s')) .$suffix. '.jpg';
    $location = $dir.'/';
    if (!is_dir(FCPATH . $location)) {
      mkdir(FCPATH . $location, 0775, true);
    }
    move_uploaded_file($_FILES[$input_name]['tmp_name'], FCPATH . $location . $file);
    return array($column => $location . $file);
    }else{
      return array();
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
        $data += $this->upload_file('attachment'  ,'/rms_dir/repo/batch/return_fund/'.$res->repo_batch_id, 'image_path');
      break;
    }
    $data += array("status_id" => $new_status);
    $this->db->where('id', $post['return_fund_id']);
    $this->db->update('tbl_repo_return_fund', $data);
    $data = array(
      'status_id'       => $new_status,
      'return_fund_id'  => $post['return_fund_id'],
      'repo_batch_id'   => $res->repo_batch_id,
      'created_by'      => $_SESSION['uid']);
    $this->db->insert('tbl_repo_return_fund_history', $data);
    $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
        return $this->error('Something Went Wrong Call Your Administrator For Assistance!');
      }
      else
      {
        $this->db->trans_commit();
        return $this->success('Repo Return Fund Updated!');
      }
  }

  function update_repo_sale()
  {
    $post = $this->input->post();
    $da   = $this->db->query("SELECT da_id,repo_sales_id from tbl_repo_sales where repo_registration_id = '".$post['edit_id']."'  limit 1")->row();
    $da_id = !empty($da->da_id) ? $da->da_id: '';
    $this->db->trans_start();
    $data = array();
    #Wrong Amount
    if($da_id == 1){
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
    if($da_id == 2){
      $data += $this->upload_file('reg_img'  ,'/rms_dir/repo/registration/'.$post['edit_id'], 'att_reg_orcr', 'registration');
      $data += $this->upload_file('ren_img'  ,'/rms_dir/repo/registration/'.$post['edit_id'], 'att_renew_or', 'renewal');
      $data += $this->upload_file('reg_trans','/rms_dir/repo/registration/'.$post['edit_id'], 'att_trans_or', 'transfer');
      $data += $this->upload_file('reg_pnp'  ,'/rms_dir/repo/registration/'.$post['edit_id'], 'att_pnp_or', 'php');
      $data += $this->upload_file('reg_ins'  ,'/rms_dir/repo/registration/'.$post['edit_id'], 'att_ins_or', 'insurance');
      $data += $this->upload_file('reg_em'   ,'/rms_dir/repo/registration/'.$post['edit_id'], 'att_em_or', 'emission');
      $data += $this->upload_file('reg_mac'  ,'/rms_dir/repo/registration/'.$post['edit_id'], 'att_macro_e_or', 'macro');
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
      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
        return $this->error('Something Went Wrong Call Your Administrator For Assistance!');
      }
      else
      {
        $this->db->trans_commit();
        switch ($da_id) {
          case 1:
            return $this->success('Repo Sales Wrong Amount Resolved!');
            break;
          case 2:
            return $this->success('Repo Sales Missing/Unreadable Attachment Resolved!');
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
      $data += $this->upload_file('file'  ,'/rms_dir/repo/batch/misc_exp/'.$post['batch_no'], 'image_path');
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
      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
        return $this->error('Something Went Wrong Call Your Administrator For Assistance!');
      }
      else
      {
        $this->db->trans_commit();
        return $this->success('Miscellaneous Updated!');
      }
    }
  }


  function insert_repo_return_fund(){
    $post = $this->input->post();
    $data = array();
    $this->db->trans_start();
    $data += $this->upload_file('attachment'  ,'/rms_dir/repo/batch/return_fund/'.$post['batch_id'], 'image_path');
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
      'created_by'      => $_SESSION['uid']);
    $this->db->insert('tbl_repo_return_fund_history', $data);
    $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
        return $this->error('Something Went Wrong Call Your Administrator For Assistance!');
      }
      else
      {
        $this->db->trans_commit();
        return $this->success('Return Fund Added!');
      }
  }

  function error($message, $title = 'Error Occured!')
  {
    $obj = new stdClass();
    $obj->message = $message;
    $obj->title   = $title;
    $obj->type    = 'error';
    return json_encode($obj);
  }

  function success($message, $title = 'Successfull')
  {
    $obj = new stdClass();
    $obj->message = $message;
    $obj->title   = $title;
    $obj->type    = 'success';
    return json_encode($obj);
  }

  function warning($message, $title = 'Warning!')
  {
    $obj = new stdClass();
    $obj->message = $message;
    $obj->title   = $title;
    $obj->type    = 'warning';
    return json_encode($obj);
  }

  function info($message, $title = 'Alert!')
  {
    $obj = new stdClass();
    $obj->message = $message;
    $obj->title   = $title;
    $obj->type    = 'info';
    return json_encode($obj);
  }
}
