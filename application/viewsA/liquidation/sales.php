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
				<table class="table">
					<thead>
						<tr>
							<th><p>Engine #</p></th>
							<th><p>Customer Name</p></th>
							<th><p>Branch</p></th>
							<th><p>Status</p></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($table as $row)
						{
							print '<tr>';
						  print '<td>'.$row->engine_no.'</td>';
						  print '<td>'.$row->first_name.' '.$row->last_name.'</td>';
						  print '<td>'.$row->branch->b_code.' '.$row->branch->name.'</td>';
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
