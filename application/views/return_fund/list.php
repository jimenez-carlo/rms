<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
	<div class="pull-left">Return Fund</div>
      </div>
      <div class="block-content collapse in">
	<form class="form-horizontal" method="post">
	  <div class="row">
	    <div class="control-group span5">
	      <div class="control-label">Date From</div>
	      <div class="controls">
                <input class="datepicker" type="text" name="date_from" value="<?php echo $date_from; ?>" autocomplete="off">
	      </div>
	    </div>
	    <div class="control-group span5">
	      <div class="control-label">Reference #</div>
	      <div class="controls">
		<?php print form_input('reference', set_value('reference')); ?>
	      </div>
	    </div>
	  </div>
	  <div class="row">
	    <div class="control-group span5">
	      <div class="control-label">Date To</div>
	      <div class="controls">
                <input class="datepicker" type="text" name="date_to" value="<?php echo $date_to; ?>" autocomplete="off">
	      </div>
	    </div>
	    <div class="control-group span5">
	      <div class="control-label">Region</div>
	      <div class="controls">
                <?php
                  $config = [ 'class' => 'form_dropdown'];
                  $val = '0';
                  if ($_SESSION['dept_name'] === 'Regional Registration') {
                    $config['disabled'] = 'true';
                    $val = $_SESSION['region_id'];
                  }
                  print form_dropdown('region', array_merge(array(0 => '- Any -'), $region), set_value('region', $val), $config);
                ?>
	      </div>
	    </div>
	  </div>
          <div class="row">
	    <div class="form-actions span5">
	  	<input type="submit" class="btn btn-success" value="Search" name="search">
	    </div>
          </div>
          <hr>
	  <table id="return-fund-table" class="table">
	    <thead>
	      <tr>
		<th>Date Entry</th>
		<th>Reference #</th>
		<th>Company</th>
		<th>Region</th>
		<th>Amount</th>
		<th>Slip</th>
		<th>Status</th>
		<th>Date Liquidated</th>
                <th>Liquidate</th>
	      </tr>
	    </thead>
	    <tbody>
	    <?php
	    foreach ($table as $row)
	    {
	      print '<tr>';
	      print '<td>'.$row->created.'</td>';
              print '<td>'.$row->reference.'</td>';
	      print '<td>'.$row->companyname.'</td>';
	      print '<td>'.$row->region.'</td>';
	      print '<td>'.$row->amount.'</td>';
	      print '<td><a href="'.base_url().'rms_dir/deposit_slip/'.$row->rfid.'/'.$row->slip.'" target="_blank">'.$row->slip.'</a></td>';
	      print '<td>'.$row->status.'</td>';
              print (empty($row->liq_date)) ? '<td>-</td>' : '<td>'.$row->liq_date.'</td>';
	      print '<td><a class="btn btn-success" href="'.base_url('return_fund/view/'.$row->rfid).'" role="button" aria-disable="false" target="_blank">View</a></td>';
	      print '</tr>';
	    }

	    if (empty($table))
	    {
	      print '<tr>';
	      print '<td></td>';
	      print '<td></td>';
	      print '<td></td>';
	      print '<td></td>';
	      print '<td>No result.</td>';
	      print '<td></td>';
	      print '<td></td>';
	      print '<td></td>';
	      print '<td></td>';
	      print '</tr>';
	    }
	    ?>
	    </tbody>
	  </table>
        </form>
      </div>
    </div>
  </div>
</div>
