<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">Pending E-Payment</div>
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
								<th><p>Addt'l Amount</p></th>
								<th><p>Document #</p></th>
								<th><p></p></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($table as $row)
							{
								print '<tr>';
								print '<td>'.$row->ref_date.'</td>';
								print '<td><a href="view/'.$row->epid.'" target="_blank">'.$row->reference.'</a></td>';
								print '<td>'.$region[$row->region].' '.$company[$row->company].'</td>';
								print '<td style="text-align: right">'.number_format($row->amount,2).'</td>';
								print '<td>'.$row->addtl_amt.'</td>';
								print '<td>'.form_input('doc_no['.$row->epid.']', set_value('doc_no['.$row->epid.']',$row->doc_no)).'</td>';
								print '<td><a href="'.base_url().'electronic_payment/print_batch/'.$row->epid.'" target="_blank" class="btn btn-success">Print</a></td>';
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
