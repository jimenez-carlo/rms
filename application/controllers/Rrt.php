<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rrt extends MY_Controller {

  public function __construct() {
     parent::__construct();
     $this->load->helper('url');
  }

	public function rrt()
	{
		$this->redirect('rrt/config');
	}

	public function config()
	{
		$this->access(11);
		$this->header_data('title', 'RRT Config');
		$this->header_data('nav', 'rrt_config');
		$this->header_data('dir', './../');

		$global = $this->load->database('global', TRUE);
		$result = $this->db->get_where('tbl_rrt')->result_object();

		foreach ($result as $key => $row)
		{
			$row->region = $global->query("select name from tbl_ph_regions
				where phrid = ".$row->region)->row();

			$cid = $row->company;
			$row->company = new Stdclass();
			$row->company->cid = $cid;
			$row->company->code = ($cid == 2) ? 6 : $cid;
			switch ($cid)
			{
				case 1: $row->company->name = 'MNC'; break;
				case 3: $row->company->name = 'HPTI'; break;
				case 6: $row->company->name = 'MTI'; break;
			}

			$result[$key] = $row;
		}

		$data['table'] = $result;
		$this->template('rrt/config', $data);
	}
}
