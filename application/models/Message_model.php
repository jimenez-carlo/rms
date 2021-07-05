<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Message_model extends CI_Model
{

  function __construct()
  {
    parent::__construct();
    if (!$this->session->has_userdata('username')) {
      show_404();
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
