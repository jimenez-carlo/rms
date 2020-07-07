<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
  function __construct() {
    parent::__construct();
    $this->load->model('Api_model', 'api');
  }

  function index() {
    if ($this->input->get("engine")) {
      echo json_encode($this->api->get_engine_data()); exit;
    }
  }
}
