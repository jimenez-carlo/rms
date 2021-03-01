<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Sales_history_model extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  public function repo($repo_inventory_id) {
    return
      $this
        ->db
        ->join('tbl_repo_history rh', 'rh.repo_inventory_id = ri.repo_inventory_id', 'left')
        ->get_where('tbl_repo_inventory ri', "ri.repo_inventory_id = {$repo_inventory_id}")
        ->result_array();
  }

}
