<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Status_model extends CI_Model {
  function __construct() {
    parent::__construct();
  }

  public function get(array $param) {
    switch ($param['format']) {
      case 'ARRAY':
        $statuses = json_decode(
          $this->db
            ->select("CONCAT('{',GROUP_CONCAT(DISTINCT '\"',st.status_id,'\":\"',st.status_name,'\"' ORDER BY FIELD(status_id, 0) DESC, status_name ASC),'}') AS statuses")
            ->from('tbl_status st')
            ->where('status_type', $param['status_type'])
            ->where('is_active', 1)
            ->get()
            ->row_array()['statuses'],
            1
        );
        break;

      default:
        $statuses = [];
    }

    return $statuses;
  }

}
