<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">
					Miscellaneous Expense
				</div>
			</div>
			<div class="block-content collapse in">

				<form class="form-horizontal" method="post">
					<fieldset>
						<div class="control-group span6">
              <div class="control-label">OR Date</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d')), array('class' => 'datepicker', 'autocomplete' => 'off')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker', 'autocomplete' => 'off')); ?>
              </div>
            </div>

						<?php
							$type = array('_any' => '- Any -') + $type;
							echo '<div class="control-group span4">';
							echo form_label('Type', 'type', array('class' => 'control-label'));
							echo '<div class="controls">';
							echo form_dropdown('type', $type, set_value('type'));
							echo '</div></div>';

							$status = array('_any' => '- Any -') + $status;
							echo '<div class="control-group span4">';
							echo form_label('Status', 'status', array('class' => 'control-label'));
							echo '<div class="controls">';
							echo form_dropdown('status', $status, set_value('status', $default_status));
							echo '</div></div>';
						?>

	          <div class="form-actions span12">
	          	<input type="submit" name="search" value="Search" class="btn btn-success" style="margin-right:10px;">

	          	<?php
	          	if ($add) print '<a href="expense/add" class="btn btn-success"><span class="icon icon-plus"></span> Add New Expense</a>';
	          	?>
	          </div>
					</fieldset>
				</form>

				<hr>
				<form class="form-horizontal" method="post">
					<table id="tbl_exp" class="table">
						<thead>
							<tr>
								<th><p>Reference # (SI/OR)</p></th>
								<th><p>OR Date</p></th>
								<th><p>Amount</p></th>
								<th><p>Type</p></th>
								<th><p>Status</p></th>
								<th><p></p></th>
							</tr>
						</thead>
						<tbody>
	            <?php
	            foreach ($table as $misc)
	            {
	            	$misc->type = ($misc->type == 'Others')
	            		? '(Others) '.$misc->other : $misc->type;
	            	$misc->status = ($misc->status == 'Rejected')
	            		? 'Rejected due to:<br>'.$misc->remarks : $misc->status;
	            	$edit_btn = ($edit && $misc->edit) ? '<input type="submit" name="edit['.$misc->mid.']" value="Edit" class="btn btn-success edit">' : '';

	              print '<tr>';
	              print '<td>'.$misc->or_no.'</td>';
	              print '<td>'.$misc->or_date.'</td>';
	              print '<td>'.$misc->amount.'</td>';
	              print '<td>'.$misc->type.'</td>';
	              print '<td>'.$misc->status.'</td>';
	              print '<td>
	              	'.form_hidden('mid', $misc->mid).'
	              	<a class="btn btn-success view">View attachments</a>
	              	'.$edit_btn.'
	              </td>';
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
<div class="modal fade" id="modal_form" role="dialog" style="width: 80%; left: 30%;">
	<div class="modal-dialog">
	  <div class="modal-content">
	  	<!-- see view.php -->
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

		$('#tbl_exp').on('click', 'a.view', function(){
			var mid = $(this).siblings('input').val();
		  $.ajax({
		    url : "expense/view",
		    type: "POST",
		    data: {'mid': mid},
		    dataType: "JSON",
		    success: function(data)
		    {
	        $('.modal-content').html(data);
	        $('#modal_form').modal('show');
		    },
		    error: function (jqXHR, textStatus, errorThrown)
		    {
		      alert('Error get data from ajax');
		    }
		  });
		});

		$('#tbl_exp').on('click', '.edit', function(){
			$('form').attr('action', 'expense/edit');
		});
		$('#tbl_exp').on('click', '.approval', function(){
			$('form').attr('action', 'expense/approve');
		});
	});
});
</script>
