<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">LTO Payment</div>
			</div>
			<div class="block-content collapse in">
				<form class="form-horizontal" method="post">
					<fieldset>
            <div class="control-group span5">
              <div class="control-label">Date</div>
              <div class="controls">
                <span style="display: inline-block; width: 50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-5 days'))), array('class' => 'datepicker')); ?>
                <br>
                <span style="display: inline-block; width: 50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker')); ?>
              </div>
            </div>

						<?php $region = array_merge(array(0 => '- Any -'), $region); ?>
						<div class="control-group span5">
							<div class="control-label">Region</div>
							<div class="controls">
								<?php 
								if(substr($_SESSION['username'],0,5) == 'ACCTG'){
								print form_dropdown('region', $region, set_value('region'));
								}else{
								print form_dropdown('region', $region, set_value('region',$_SESSION['region']), array('readonly'=>'true'));
								}
								//print form_dropdown('region', $region, set_value('region'));
								
								?>
							</div>
						</div>

						<div class="control-group span5">
							<div class="control-label">Status</div>
							<div class="controls">
								<?php print form_dropdown('status', array_merge(array(0 => '- Any -'), $status), set_value('status')); ?>
							</div>
						</div>

						<div class="form-actions span5">
							<input type="submit" class="btn btn-success" value="Search" name="search">
							<?php if(substr($_SESSION['username'],0,5) != 'ACCTG'): ?>
							<a href="lto_payment/add" target="_blank" class="btn btn-success">Add New Batch</a>
							<?php endif; ?>
						</div>

						<div class="control-group span5">
							<div class="control-label">Payment Reference #</div>
							<div class="controls">
								<?php print form_input('reference', set_value('reference')); ?>
							</div>
						</div>
					</fieldset>

					<hr>

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
								<th><p>Payment Status</p></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($table as $row)
							{
								print '<tr>';
								print '<td>'.$row->ref_date.'</td>';
								print '<td><a href="lto_payment/view/'.$row->lpid.'" target="_blank">'.$row->reference.'</a></td>';

								print '<td>'.$region[$row->region].' '.$company[$row->company].'</td>';
								print '<td>'.number_format($row->amount, 2, '.', ',').'</td>';

								if (empty($row->doc_no)) print '<td>Pending</td>';
								else print '<td>'.$row->doc_no.'<br><i>on '.$row->doc_date.'</i></td>';

								if (empty($row->dm_no)) print '<td>-</td>';
								else print '<td>'.$row->dm_no.'<br><i>on '.$row->dm_date.'</i></td>';

								if (empty($row->confirmation)) print '<td>-</td>';
								else print '<td>'.$row->confirmation.'<br><a class="receipt">'.$row->receipt.'</a><br><i>on '.$row->deposit_date.'</i></td>';

								print '<td>'.$status[$row->status].'</td>';
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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog" style="width: 85%; left: 30%;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">&nbsp;</h3>
      </div>
      <div class="modal-body"><img></div>
      <div class="modal-footer"></div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
    $(".table").on('click', 'a.receipt', function(){
      $('#modal_form .modal-body img').attr('src', '/rms_dir/lto_receipt/<?php print $payment->lpid.'/'.$payment->receipt; ?>');
      $('#modal_form').modal('show');
    });
	});
});
</script>