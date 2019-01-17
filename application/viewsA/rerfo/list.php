<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">Rerfo</div>
			</div>
			<div class="block-content collapse in">
				<table class="table" style="margin:0">
					<thead>
						<tr>
							<th>Transaction Number</th>
							<th>Branch</th>
							<th>Registration Date</th>
							<th>Date Printed</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($table as $key => $rerfo)
						{
							print '
							<tr>
								<td>'.$rerfo->trans_no.'</td>
								<td>'.$rerfo->branch->b_code.' '.$rerfo->branch->name.'</td>
								<td>'.$rerfo->date.'</td>
								<td>'.$rerfo->print_date.'</td>
								<td>
									<a href="./rerfo/view/'.$rerfo->rid.'" class="btn btn-success">View</a> ';
							
	            if ($rerfo->print == 0) {
	              print '<a href="./rerfo/sprint/'.$rerfo->rid.'" class="btn btn-success" onclick="return confirm('."'Are you sure you want to save and print?'".')">Print</a>';
	            }
	            else {
	              print '<a href="./rerfo/request/'.$rerfo->rid.'" class="btn btn-success">Request Reprinting</a>';
	            }

							print '</td>
							</tr>';
						}

						if (empty($table))
						{
							print '
								<tr>
									<td>No result.</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>