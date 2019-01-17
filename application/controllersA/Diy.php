<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diy extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
		$this->load->helper('directory');
  }

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'DIY');
		$this->header_data('nav', 'diy');
		$this->header_data('dir', './');

		$submit = $this->input->post('submit');
		if (!empty($submit))
		{
			foreach ($submit as $key => $val)
			{
				$diy = $this->db->get_where('tbl_diy', array('did' => $key))->row();

				if ($diy->status == 1)
				{
					$diy->status = 2;
					$this->db->update('tbl_diy', $diy, array('did' => $key));
				}

				redirect('../'.$diy->path);
			}
		}

		/*
		$date = date('Y-m-d', strtotime('-6 days'));
		$files = $this->db->query("select * from tbl_diy
			where left(date, 10) = '$date'")->result_object();
		*/

		$files = $this->db->query("select * from tbl_diy")->result_object();

		$data['files'] = $files;
		$this->template('diy/list', $data);
	}
}