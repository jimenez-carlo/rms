<?php

class Caching_model extends CI_Model{

  public function delete($code) {
    switch ($code) {
      case 'DA_REASON':
        $id = 1;
        $sql = <<<SQL
          SELECT
            (SELECT COUNT(*) FROM  tbl_status WHERE status_type = 'DA') count,
            (SELECT count FROM tbl_handle_caching) old_count
SQL;
        break;
    }

    $count = $this->db->query($sql)->row();

    if ($count->count !== $count->old_count) {
      $this->db->update(
        'tbl_handle_caching',
        ['count' => $count->count],
        "cache_id = {$id}"
      );
      return true;
    } else {
      return false;
    }
  }

}
