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

				<form class="form-horizontal" method="post" style="margin:10px 0px">
					<fieldset>
            <div class="control-group span5">
              <div class="control-label">Registration Date</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-3 days'))), array('class' => 'datepicker')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker')); ?>
              </div>
            </div>

            <?php
							$branches = array('_any' => '- Any -') + $branches;
							echo '<div class="control-group span5">';
							echo form_label('Branch', 'branch', array('class' => 'control-label'));
							echo '<div class="controls">';
							echo form_dropdown('branch', $branches, set_value('branch'));
							echo '</div></div>';

							$status = array(0 => '- Any -', 1 => 'Pending validation', 2 => 'Pending topsheet', 3 => 'With topsheet');
							echo '<div class="control-group span5">';
							echo form_label('Status', 'status', array('class' => 'control-label'));
							echo '<div class="controls">';
							echo form_dropdown('status', $status, set_value('status'));
							echo '</div></div>';

							// $print_status = array(
							// 	'_any' => '- Any -',
							// 	0 => 'For printing',
							// 	1 => 'Printed',
							// );
							// echo '<div class="control-group span4">';
							// echo form_label('Print Status', 'print', array('class' => 'control-label'));
							// echo '<div class="controls">';
							// echo form_dropdown('print', $print_status, set_value('print', 0));
							// echo '</div></div>';
						?>

            <div class="form-actions span12">
            	<input type="submit" name="search" value="Search" class="btn btn-success">
            </div>
					</fieldset>

					<hr>
					<table class="table" style="margin:0">
						<thead>
							<tr>
								<th>Transaction Number</th>
								<th>Branch</th>
								<th>Registration Date</th>
								<th>Topsheet</th>
								<th>Log</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($table as $key => $rerfo)
							{
								$key = '['.$rerfo->rid.']';
								$log = (!empty($rerfo->print_date))
									? 'Printed on '.$rerfo->print_date : '<i>For printing</i>';

								print '<tr>';
								print '<td>'.$rerfo->trans_no.'</td>';
								print '<td>'.$rerfo->bcode.' '.$rerfo->bname.'</td>';
								print '<td>'.$rerfo->date.'</td>';
								print '<td>'.$rerfo->topsheet.'</td>';
								print '<td>'.$log.'</td>';

								print '<td>';
								print '<input type="submit" name="view'.$key.'" value="View" class="btn btn-success view"> ';

	              $print_date = (!empty($print_date)) ? substr($rerfo->print_date, 0, 10) : date('Y-m-d');
	              if ($rerfo->print == 0 || $print_date == date('Y-m-d')) {
		              print '<input type="submit" name="print'.$key.'" value="Print" class="btn btn-success print">';
		            }
		            else {
		              print '<input type="submit" name="request'.$key.'" value="Request Reprinting" class="btn btn-success request">';
		            }
								print '</td>';
								print '</tr>';
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
										<td></td>
									</tr>';
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

		$('.table').on('click', '.view', function(){
			$('form').attr('action', 'rerfo/view').removeAttr('target');
		});
		$('.table').on('click', '.print', function(){
			$('form').attr('action', 'rerfo/sprint').attr('target', '_blank');
		});
		$('.table').on('click', '.request', function(){
			$('form').attr('action', 'rerfo/request').removeAttr('target');
			return confirm('The following action cannot be undone: Request reprinting of rerfo. Continue?');
		});
	});
});
</script>
