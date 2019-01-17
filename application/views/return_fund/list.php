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
					<fieldset>
						<div class="control-group span5">
							<div class="control-label">Region</div>
							<div class="controls">
								<?php print form_dropdown('region', array_merge(array(0 => '- Any -'), $region), set_value('region')); ?>
							</div>
						</div>

						<div class="control-group span5">
							<div class="control-label">Reference #</div>
							<div class="controls">
								<?php print form_input('reference', set_value('reference')); ?>
							</div>
						</div>

						<div class="form-actions span5">
							<input type="submit" class="btn btn-success" value="Search" name="search">
						</div>
					</fieldset>

					<hr>

					<table class="table">
						<thead>
							<tr>
								<th><p>Date Entry</p></th>
								<th><p>Reference #</p></th>
								<th><p>Company</p></th>
								<th><p>Region</p></th>
								<th><p>Amount</p></th>
								<th><p>Slip</p></th>
								<th><p>Date Liquidated</p></th>
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
								print '<td>'.$region[$row->fund].'</td>';
								print '<td>'.number_format($row->amount, 2, '.', ',').'</td>';
								print '<td><a href="/rms_dir/deposit_slip/'.$row->rfid.'/'.$row->slip.'" target="_blank">'.$row->slip.'</a></td>';

								if (empty($row->liq_date)) print '<td>-</td>';
								else print '<td>'.$row->liq_date.'</td>';
								print '</tr>';
							}

							if (empty($table))
							{
								print '<tr>';
								print '<td>No result.</td>';
								print '<td></td>';
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

<script type="text/javascript">
$(function(){
	$(document).ready(function(){
		$(".table").dataTable({
			"sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
			"sPaginationType": "bootstrap",
			"oLanguage": {
				"sLengthMenu": "_MENU_ records per page"
			},
			"bFilter": false,
			"bSort": false,
			"iDisplayLength": 5,
			"aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
		});
	});
});
</script>