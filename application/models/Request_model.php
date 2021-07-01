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

  function expense_type()
  {
    return $this->db->query("SELECT `type` from tbl_misc_type")->result_array();
  }

  function batch_dropdown()
  {
    return $this->db->query("SELECT repo_batch_id as `value`,reference as display from tbl_repo_batch where bcode = {$_SESSION['branch_code']}")->result_array();
  }

  function update_misc()
  {
    $post = $this->input->post();
    if ($post['edit_id']) {

      $data = array();
      $location = null;
      if (!empty($_FILES['file']['size'])) {
        $file = md5($_SESSION['branch_code'] . date('Y-m-d H:m:s')) . '.jpg';
        $location = '/rms_dir/repo/batch/misc_exp/' . $post['batch_no'] . '/';

        if (!is_dir(FCPATH . $location)) {
          mkdir(FCPATH . $location, 0775, true);
        }
        move_uploaded_file($_FILES['file']['tmp_name'], FCPATH . $location . $file);
        $data += array("image_path" => $location . $file);
      }
      $this->db->trans_start();
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
