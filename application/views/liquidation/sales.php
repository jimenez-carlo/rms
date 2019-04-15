<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Liquidation</div>
      </div>
      <div class="block-content collapse in">
      	<form class="form-horizontal" action="csv/<?php print $vid; ?>" target="_blank">
      		<div class="form-actions">
	      		<input type="hidden" name="vid" value="<?php print $vid; ?>">
	      		<input type="submit" name="submit" value="Download as CSV" class="btn btn-success">
	      		<a href="/return_fund/ca/<?php print $vid; ?>" class="btn btn-success">Return Fund</a>
      		</div>
      	</form>

				<table class="table">
					<thead>
						<tr>
							<th><p>Date Sold</p></th>
							<th><p>Branch</p></th>
							<th><p>Customer Name</p></th>
							<th><p>Customer Code</p></th>
							<th><p>Engine #</p></th>
							<th><p>Type of Sales</p></th>
							<th><p>SI #</p></th>
							<th><p>Registration Type</p></th>
							<th><p>AR #</p></th>
							<th><p>Amount Given</p></th>
							<th><p>LTO Registration</p></th>
							<th><p>Status</p></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sales_type = array(0 => 'Brand New (Cash)', 1 => 'Brand New (Installment)');
						foreach ($table as $row)
						{
							print '<tr>';
						  print '<td>'.substr($row->date_sold, 0, 10).'</td>';
						  print '<td>'.$row->bcode.' '.$row->bname.'</td>';
						  print '<td>'.$row->first_name.' '.$row->last_name.'</td>';
						  print '<td>'.$row->cust_code.'</td>';
						  print '<td>'.$row->engine_no.'</td>';
						  print '<td>'.$sales_type[$row->sales_type].'</td>';
						  print '<td>'.$row->si_no.'</td>';
						  print '<td>'.$row->registration_type.'</td>';
						  print '<td>'.$row->ar_no.'</td>';
						  print '<td>'.$row->amount.'</td>';
						  print '<td>'.$row->registration.'</td>';
						  print '<td>'.$row->status.'</td>';
						  print '</tr>';
						}
						?>
					</tbody>
				</table>
      </div>
    </div>
  </div>
</div>
