<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">
					Checks
				</div>
			</div>
			<div class="block-content collapse in">
			
				<form class="form-horizontal" method="post" style="margin:10px 0px">
					<fieldset>
            <div class="control-group span5">
              <div class="control-label">Check Date</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d')), array('class' => 'datepicker')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker')); ?>
              </div>
            </div>

            <?php
							$status = array_merge(array('_any' => '- Any -'), $status);
							echo '<div class="control-group span4">';
							echo form_label('Status', 'status', array('class' => 'control-label'));
							echo '<div class="controls">';
							echo form_dropdown('status', $status, set_value('status'));
							echo '</div></div>';
						?>

            <div class="form-actions span12">
            	<input type="submit" name="search" value="Search" class="btn btn-success">
            </div>
					</fieldset>

					<hr>
					<table class="table" style="margin:0">
						<thead>
							<tr>
								<!-- <th><p>Company</p></th> -->
								<th><p>Check Number</p></th>
								<th><p>Check Date</p></th>
								<th><p>Amount</p></th>
								<th><p>Status</p></th>
								<th><p></p></th>
							</tr>
						</thead>
						<tbody>
	            <?php
	            foreach ($table as $check)
	            {
	              print '<tr>';
	              // print '<td>'.$check->company.'</td>';
	              print '<td>'.$check->check_no.'</td>';
	              print '<td>'.$check->check_date.'</td>';
	              print '<td>'.$check->amount.'</td>';

	              switch ($check->status)
	              {
	              	case 'On hold check':
	              		print '<td>'.$check->status.' on '.$check->hold_date.', due to:<br>'.$check->reason.'</td>';
	              		print '<td><input type="submit" name="unhold['.$check->cid.']" value="Unhold" class="btn btn-success" onclick="return confirm(\'Check # '.$check->check_no.' will be unhold. Continue?\')"></td>';
	              		break;
	              	case 'Used check':
	              		print '<td>'.$check->status.' on '.$check->used_date.'</td>';
	              		print '<td></td>';
	              		break;
	              	default:
	              		print '<td>'.$check->status.'</td>';
	              		print '<td><input type="button" name="hold" value="Hold" class="btn btn-success" onclick="hold_c('.$check->cid.')"></td>';
	              }
	              print '</tr>';
	            }

	            if (empty($table))
	            {
	              print '<tr>
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
				</form>
			</div>
		</div>
	</div>
</div> 

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h3 class="modal-title">Hold check</h3>
	    </div>
	    <div class="modal-body form">
	      <div class="alert alert-error">
	        <button class="close" data-dismiss="alert">&times;</button>
	        <div class="error"></div>
	      </div>
	      <div class="form-body">
	      	<form action="#" id="form" class="form-horizontal">
	      		<input type="hidden" name="cid">

	          <div class="form-group" style="margin-bottom:15px;">
	            <label class="control-label" style="margin-right:10px;">Reason for hold</label>
	            <div class="controls"><textarea name="reason"></textarea></div>
	          </div>
	      	</form>
       	</div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnHold" onclick="hold_check()" class="btn btn-success">Hold</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
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
	});
});
</script>