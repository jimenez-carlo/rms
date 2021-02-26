<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">Liquidation E-Payment</div>
			</div>
			<div class="block-content collapse in">
				<form class="form-horizontal" method="post">
					<fieldset>
						<div class="form-actions">
							<input type="submit" class="btn btn-success" value="Save" name="save" onclick="return confirm('Please make sure all information are correct before proceeding. Continue?')">
						</div>
					</fieldset>

					<table class="table">
						<thead>
							<tr>
								<th><p>Date</p></th>
								<th><p>Payment Reference #</p></th>
								<th><p>Region</p></th>
								<th><p>Amount</p></th>
								<th><p>Document #</p></th>
								<th><p>Debit Memo #</p></th>
								<th><p>Payment Confirmation #</p></th>
								<th><p>Encoded Registration</p></th>
								<th><p>Balance</p></th>
								<th><p>Liquidated</p></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($table as $row)
							{
								print '<tr>';
								print '<td>'.$row->ref_date.'</td>';
								print '<td><a href="view/'.$row->epid.'" target="_blank">'.$row->reference.'</a></td>';
								print '<td>'.$row->region.' '.$row->company.'</td>';
								print '<td>'.$row->amount.'</td>';
								print '<td>'.$row->doc_no.'</td>';
								print '<td>'.$row->dm_no.'</td>';
								print '<td>'.$row->confirmation.'</td>';
								print '<td>'.$row->sales.'</td>';
								print '<td>'.($row->amount - $row->sales).'</td>';
								print '<td><input type="checkbox" name="liquidated[]" value="'.$row->epid.'"></td>';
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
