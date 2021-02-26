<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Form_model extends CI_Model {
  function __construct() {
    parent::__construct();
    $this->db->query("SET SESSION group_concat_max_len = 18446744073709551615");
  }

  public function branch_input_drop_down() {
    return json_decode(
      $this->db->query("
        SELECT
          CONCAT(
            '{',
            GROUP_CONCAT( DISTINCT CONCAT('\"',bcode,',',bname,'\": \"', CONCAT(bcode, ' - ', bname),'\"') ORDER BY bcode),
            '}'
          ) AS branches
        FROM (
          SELECT s.bcode, ANY_VALUE(TRIM(CHAR(9) FROM TRIM(s.bname))) AS bname FROM tbl_sales s GROUP BY s.bcode
        ) AS x
      ")
      ->row_array()['branches'],
      1
    );
  }
}
