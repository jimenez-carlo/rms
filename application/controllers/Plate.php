<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plate extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Plate_model', 'plate');
		$this->load->model('Rerfo_model', 'rerfo');
		$this->load->model('Sales_model', 'sales');
		$this->load->model('File_model', 'file');
	}

	public function index()
	{
		$this->access(1);
		$this->header_data('title', 'Plate');
		$this->header_data('nav', 'plate');
		$this->header_data('dir', base_url());

		$param = new Stdclass();
		$param->region = $_SESSION['region'];
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');
		$param->branch = $this->input->post('branch');
		$param->status = $this->input->post('status');
		$param->print = $this->input->post('print');

		//Approve Plate ID
		$data['sid'] = $this->input->post('sid');
		if($this->input->post('plate_id')){
			$pid=$this->input->post('plate_id');
			$this->updateplatestatus($pid, 2);
			redirect('plate');
		}
		//Update Plate No only
		$plateno = $this->input->post('plateno');
		if (!empty($plateno)) {
			$pid = $this->input->post('plateid');
			$pno = $this->input->post('plateno');
			$this->updatePlateNumber($pid,$pno);
			redirect('plate');
		}
		$data['branches'] = $this->rerfo->dd_branches($_SESSION['region']);
		$data['table'] = $this->plate->list_rerfo($param);
		$this->template('plate/plate', $data);

	}

	public function branch_list()
	{
		$this->access(1);
		$this->header_data('title', 'Plate Transmittal');
		$this->header_data('nav', 'Plate Transmittal');
		$this->header_data('dir', base_url());

		$param = new Stdclass();
		$param->date_from = $this->input->post('date_from');
		$param->date_to = $this->input->post('date_to');
		$param->branch = $this->input->post('branch');
		$param->status = $this->input->post('status');
		$param->print = $this->input->post('print');
		 if ($_SESSION['position']=='108' || $_SESSION['position']=='109' || $_SESSION['position']=='156'){
		 	$param->region = $_SESSION['region'];
		$data['branches'] = $this->sales->dd_branches();
	}

		$data['table'] = $this->plate->get_branch_transmittal($param);
		$this->template('plate/plate_transmittal', $data);
	}

	public function view()
	{

                $this->access(1);
		$this->header_data('title', 'List of Customer');
		$this->header_data('nav', 'Plate Transmittal');
		$this->header_data('dir', base_url());

				//$data['sid'] = $this->input->post('sid');
		if($this->input->post('submit')){
                  foreach ($this->input->post('submit') as $key => $value){
                    $this->updateplatestatus($key, 2);
                  }
  		  redirect('plate/branch_list');
	        }

                if($this->input->post('approve')){
                  foreach ($this->input->post('checkbox') as $key => $value){
                    $this->updateplatestatus($value, 2);
                  }
                  redirect('plate/branch_list');
                }

		$plateno = $this->input->post('plateno');
		if (!empty($plateno)) {
			$pid = $this->input->post('plateid');
			$pno = $this->input->post('plateno');
			$this->updatePlateNumber($pid,$pno);
			redirect('plate/branch_list');
		}

                if($this->input->post('submitt') && $this->input->post('test')){
                  $date = $this->input->post('test');
                  foreach ($this->input->post('submitt') as $key => $value){
                    $this->db->query("UPDATE tbl_plate
                      SET
                      status_id = 4,
                      received_cust = '$date'
                      WHERE
                      plate_id = $key");
                  }
                  redirect('plate/branch_list');
                }

                if($this->input->post('submittt')){
                  foreach ($this->input->post('submittt') as $key => $value){
                    $this->db->query("UPDATE tbl_plate
                      SET
                      status_id = 3,
                      received_dt = NOW()
                      WHERE
                      plate_id = $key");
                  }
                  redirect('plate/branch_list');
                }

                if($this->input->post('edit')){
                  foreach ($this->input->post('edit') as $key => $value){
                    $pid = $key;
                    $pno = $this->input->post('plateno'.$key);
                    $sql = $this->db->query("SELECT * FROM tbl_plate WHERE plate_number = '$pno'")->result_array();
                    if (empty($sql)){
                      $this->updatePlateNumber($pid,$pno);
                    }
                    else{
                      $this->session->set_flashdata(
                        'alert',
                        '<div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Update Failed!</strong>
                        Plate number already exists. Please enter a unique plate number.
                        </div>'
                      );
                    }
                }
                redirect('plate/branch_list');
        }


		if($this->input->post('view_te')){
			$view = $this->input->post('view_te');
		}
		else{
			$view = $this->input->post('view_tr');
		}
		$bid = $this->input->post('bid');// or show_404();

		$status = $this->input->post('vstatus');
		$date = $this->input->post('vdate');

		if (!empty($view)){
			$pid = current(array_keys($view));

		}

                foreach ($date as $key => $value){
                  if($key == $pid){
                    $test = $value;
                  }
                }

                $result = $this->db->query("SELECT
                  b.branch AS bid
                  FROM
                  tbl_plate AS a
                  INNER JOIN
                  tbl_sales AS b ON a.sid = b.sid
                  WHERE
                  plate_id = $pid;
                ")->result();
		$bid = $result[0]->bid;
                $data['table'] = $this->plate->load_platetransmittal($bid, $test, $status);
		$data['bid'] = $bid;
		if($this->input->post('view_te')){
			if(empty($status)){
                	$status = 0;
                }
			redirect('plate/plate_transmittal/'.$bid.'/'.$test.'/'.$status);

		}
		else{

			$this->template('plate/view', $data);
		}
	}

	public function UpdatePlate_BS()
	{
		$this->access(1);
		$this->header_data('title', 'Update Plate');
		$this->header_data('nav', 'update_plate');
		$this->header_data('dir', base_url());

		$param = new Stdclass();
		$param->name = $this->input->post('name');
		$param->engine_no = $this->input->post('engine_no');
		$param->branch = $this->input->post('branch');
		$param->user_access = '1';

		if (empty($param->branch) && !is_numeric($param->branch) && ($_SESSION['position'] == 72 || $_SESSION['position'] == 73 || $_SESSION['position'] == 81)) {
			$param->branch = $_SESSION['branch'];
		}

		//Update Plate No only
		$plateno = $this->input->post('plateno');
		$sql = "SELECT * FROM tbl_plate WHERE plate_number = '".$plateno."'";
		$quee = $this->db->query($sql)->result();
		if (!empty($plateno) ) {
			if(empty($quee)){
			$sid = $this->input->post('plateid');
			$pno = $this->input->post('plateno');
                        $result = $this->db->query("
                          SELECT
			    b.branch as bid,
			    b.bcode,
			    b.bname AS branchname
			  FROM
			    tbl_sales AS b
			  WHERE b.sid = '$sid'
			")->result();
			$ptn = $result[0]->bcode;
			$this->addPlateNumber($sid,$plateno,$ptn);
			$_SESSION['messages'][] = 'Plate number encoded successfully.';
			redirect('plate/UpdatePlate_BS');
		}
		else{
			echo "<script>
			alert('There is an existing plate number!');
				</script>";
			redirect('plate/UpdatePlate_BS');
		}
	}

		$data['branch_def'] = ($_SESSION['position'] == 73 || $_SESSION['position'] == 81) ? $_SESSION['branch'] : 0;
		$data['branches']   = $this->sales->dd_branches();
		$data['status']     = $this->sales->status;
		$data['table']      = $this->plate->plate_report($param);

		$this->template('plate/update_plate', $data);
	}


        public function pending_list()
	{
		$this->access(1);
		$this->header_data('title', 'Update Plate');
		$this->header_data('nav', 'update_plate');
		$this->header_data('dir', base_url());

		$param = new Stdclass();
		$param->name = $this->input->post('name');
		$param->engine_no = $this->input->post('engine_no');
		$param->branch = $this->input->post('branch');
		$param->user_access = '1';

		if (empty($param->branch) && !is_numeric($param->branch) && ($_SESSION['position'] == 72 || $_SESSION['position'] == 73 || $_SESSION['position'] == 81)) {
			$param->branch = $_SESSION['branch'];
		}

		//Update Plate No only
		$plateno = $this->input->post('plateno');
		$sql = "SELECT * FROM tbl_plate WHERE plate_number = '".$plateno."'";
		$quee = $this->db->query($sql)->result();
		if (!empty($plateno) ) {
			if(empty($quee)){
			  $sid = $this->input->post('plateid');
			  $pno = $this->input->post('plateno');
                          $result = $this->db->query("
                            SELECT
			      b.branch as bid,
			      b.bcode,
			      b.bname AS branchname
			    FROM
			      tbl_sales AS b
			    WHERE b.sid = '$sid'
			  ")->result();
			  $ptn = $result[0]->bcode;
			  $this->addPlateNumber($sid,$plateno,$ptn);
			  redirect('plate/UpdatePlate_BS');
		        }
		        else{
			  echo "<script>alert('There is an existing plate number!');</script>";
			  redirect('plate/UpdatePlate_BS');
		        }
                }

		$data['branch_def'] = ($_SESSION['position'] == 73 || $_SESSION['position'] == 81) ? $_SESSION['branch'] : 0;
		$data['branches']   = $this->sales->dd_branches();
		$data['status']     = $this->sales->status;
		$data['table']      = $this->plate->plate_report_bak($param);
		$this->template('plate/pending_list', $data);
	}

	public function plate_transmittal($bid, $test, $status) {
                $data['topsheet'] = $this->plate->print_platetransmittal($bid, $test, $status);
                $this->load->view('plate/plate_transmittal_print', $data);
	}


	public function updateplatestatus($plateid, $status){
		$this->plate->update_plateStatus($plateid, $status);
	}

	public function updatePlateNumber($plateid, $plateno){
		$this->plate->update_platenumber($plateid, $plateno);
	}

	public function addPlateNumber($sid,$plateno, $ptn){
		$this->plate->add_platenumber($sid,$plateno, $ptn);
	}

	public function approve_all()
 	{
  		if($this->input->post('checkbox_value'))
  		{
   			$id = $this->input->post('checkbox_value');
   			for($count = 0; $count < count($id); $count++)
   			{
    			$this->updateplatestatus($id[$count], 2);
   			}
  		}
 	}

}
