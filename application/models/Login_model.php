<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model{

        public function __construct() {
                parent::__construct();
        }

        public function add_user_log($username, $firstname, $lastname) {

          $ip_address = $_SERVER['REMOTE_ADDR'];
          $time_stamp = date('Y-m-d H:i:s', time());

          $sql = <<<SQL
             INSERT INTO tbl_user_logs (
               user,
               user_name,
               ip_address,
               datetime_in
             ) VALUES (
               "$firstname $lastname",
               "$username",
               "$ip_address",
               "$time_stamp"
             )
SQL;
          $this->db->query($sql);

          $result = $this->db->query("SELECT
            ulid
            FROM tbl_user_logs
            ORDER BY ulid DESC LIMIT 1");

          return $result->row();

        }

        public function add_user_custom_log($username="") {
          $this->db->query("INSERT INTO tbl_user_logs (
            user,
            ip_address,
            user_name,
            datetime_in
          )
          VALUES (
            '$username',
            '".$_SESSION['firstname']." ".$_SESSION['middlename']." ".$_SESSION['lastname']."',
            '".$_SERVER['REMOTE_ADDR']."',
            '".date('Y-m-d H:i:s', time())."'
          )");

          $result = $this->db->query("SELECT
            ulid
            FROM tbl_user_logs
            ORDER BY ulid DESC LIMIT 1")->row();

          return $result;

        }


        public function end_user_log($ulid="") {
          $this->db->query("UPDATE
                              tbl_user_logs
                            SET
                              datetime_out='".date('Y-m-d H:i:s', time())."'
                            WHERE ulid=$ulid");
        }

        public function saveLog($action="") {
                $ulid = (isset($_SESSION['ulid'])) ? $_SESSION['ulid'] : 0;
                $this->db->query("INSERT INTO tbl_user_action_logs (
                                        userlid,
                                        action_taken,
                                        datetime_log
                                )
                                VALUES (
                                ".$ulid.",
                                '" . str_replace("'", "\'", $action) . "',
                                '" . date('Y-m-d H:i:s', time()) . "')");
        }

        public function get_user_info($username) {
          $global = $this->load->database('global', TRUE);

          $query  = <<<SQL
            SELECT
              u.username, u.password, b.bid, b.b_code
              ,CONCAT(c.code, ' ', b.name) AS branch_name
              ,b.rm, b.am_ccod, b.am_csod, b.ch, b.hrbp, b.address,
              p.pid AS position_id, p.name AS position_name,
              ui.*, d.did AS dept_id, d.description AS dept_name,
              c.cid AS company_id, c.code AS company_code
              ,rr.*
            FROM
              tbl_users u
            INNER JOIN
              tbl_users_info ui ON u.uid=ui.uid
            INNER JOIN
              tbl_departments d ON ui.department = d.did
            LEFT JOIN
              tbl_branches b ON ui.branch = b.bid
            LEFT JOIN
              tbl_positions p ON ui.position = p.pid
            LEFT JOIN
              tbl_companies c ON b.company = c.cid
            LEFT JOIN
              tbl_rrt_region rr ON rr.rrt_region_id = b.rrt_region_id
            WHERE
              username="$username"
SQL;

          $result = $global->query($query);
          return $result->row();
        }

        public function get_system_access($system="") {
          $global = $this->load->database('global', TRUE);
          $result = $global->query("SELECT * FROM tbl_system_access tsa WHERE tsa.system = '$system'");

          return $result->result_array();
        }

        public function get_access() {
          $result = $this->db->query("SELECT * FROM tbl_page_access");
          return $result->result_array();
        }

        public function decrypt($password="") {
          $key = 'passwordforportal';

          $data = base64_decode($password);
          $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

          return $decrypted_password = rtrim(
            mcrypt_decrypt(
              MCRYPT_RIJNDAEL_128,
              hash('sha256', $key, true),
              substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
              MCRYPT_MODE_CBC,
              $iv
            ),
            "\0"
          );
        }

        public function encrypt($password="") {
          $key = 'passwordforportal';

          $iv = mcrypt_create_iv(
              mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
              MCRYPT_DEV_URANDOM
          );

          return $encrypted_password = base64_encode(
            $iv .
            mcrypt_encrypt(
              MCRYPT_RIJNDAEL_128,
              hash('sha256', $key, true),
              $password,
              MCRYPT_MODE_CBC,
              $iv
            )
          );
        }

        public function get_region_and_fund($user_id)
        {
          $this->db->select('fund.fid AS fund_id, rur.region_id, r.region');
          $this->db->from('tbl_rrt_users_region rur');
          $this->db->join('tbl_region r', 'rur.region_id = r.rid', 'inner');
          $this->db->join('tbl_fund fund', 'r.rid = fund.region', 'inner');
          $this->db->where('user_id', $user_id);
          $region = $this->db->get()->row_array();
          return $region;
        }

}
