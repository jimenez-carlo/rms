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
							<div class="control-label">Company</div>
							<div class="controls">
								<?php print form_dropdown('company', $company, set_value('company', 1)); ?>
							</div>
						</div>

						<div class="form-actions span5">
							<input type="submit" class="btn btn-success" value="Save" name="save" onclick="return confirm('Please make sure all information are correct before proceeding. Continue?')">
						</div>
					</fieldset>

					<hr>

					<table class="table">
						<thead>
							<tr>
								<th><p></p></th>
								<th><p>Branch</p></th>
								<th><p>Customer Name</p></th>
								<th><p>Engine #</p></th>
								<th><p>Chassis #</p></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($table as $row)
							{
								print '<tr>';
								print '<td><input type="checkbox" name="sales[]" value="'.$row->sid.'"></td>';
								print '<td>'.$row->bcode.' '.$row->bname.'</td>';
								print '<td>'.$row->first_name.' '.$row->last_name.'</td>';
								print '<td>'.$row->engine_no.'</td>';
								print '<td>'.$row->chassis_no.'</td>';
								print '</tr>';
							}

							if (empty($table))
							{
								print '<tr>';
								print '<td></td>';
								print '<td>No result.</td>';
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