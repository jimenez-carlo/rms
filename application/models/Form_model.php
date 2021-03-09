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

  public function region_dropdown(string $format = '') {
    switch ($format) {
      case 'WITH_OUT_ANY':
        $with_or_without_any = "";
        break;

      default:
        $with_or_without_any = "'\"0\": \"- Any -\",',";
    }
    return json_decode(
      $this->db
      ->select("
        CONCAT('{',
          {$with_or_without_any}
          GROUP_CONCAT('\"',rid,'\"', ':\"',region,'\"')
        ,'}') AS regions
      ")
      ->get('tbl_region')
      ->row_array()['regions'],
      true
    );
  }

  public function ca_dropdown(string $type) {
    switch ($type) {
      case 'REPO':
        $result = $this->db
        ->select("
          CONCAT('{',
            GROUP_CONCAT('\"',rb.repo_batch_id,'\"', ':\"',rb.reference,'\"')
          ,'}') AS ref_list
        ")
        ->get_where("tbl_repo_batch rb", "rb.bcode={$_SESSION['branch_code']} AND rb.status <> 'LIQUIDATED'")
        ->row_array()['ref_list'];
    }

    return json_decode($result, true);
  }
}
