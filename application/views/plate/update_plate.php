<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">Customer Status</div>
			</div>
			<div class="block-content collapse in">
			
				<form class="form-horizontal" method="post">
					<?php print form_hidden('sid', 0); ?>

					<fieldset>
						<?php
								echo '<div class="control-group span5">';
								echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
								echo '<div class="controls">';
								echo form_input('engine_no', set_value('engine_no'));
								echo '</div></div>';
								
						?>
						<div class="form-actions span12">
							<input type="submit" class="btn btn-success" value="Search" name="submit">
						</div>
					</fieldset>
</form>
<form id="form_plateno" role="form" name="modalForm" class="form-horizontal" method="post">
					<hr>
							<?php
							if(!empty($table)){
							foreach ($table as $sales)
							{
								
								print '<table class="table"><tbody>';
								print '<tr><td style="color:gray">Branch:</td><td>'.$sales->bcode.' '.$sales->bname.'</td></tr>';
								print '<tr><td style="color:gray">Engine Number:</td><td>'.$sales->engine_no.'</td></tr>';
								print '<tr><td style="color:gray">Customer Name:</td><td>'.$sales->first_name.' '.$sales->last_name.'</td></tr>';
								print '<tr><td style="color:gray">MV File:</td><td>'.$sales->mvf_no.'</td></tr>';
								print '<tr><td style="color:gray">Status:</td><td>'.$sales->status_name.'</td></tr>';
								print '<tr><td style="color:gray">Plate Number:</td><td><input name="plateno" class="form-control" id="md_plateno" value="" maxlength="10" /><input name="plateid" class="form-control" id="md_sid" value="'.$sales->ssid.'" style="visibility: hidden;"/></td></tr>';
								print '</tbody></table>';
								print '<input type="submit" class="btn btn-success" value="Save Changes" name="submit">';
								//print ' <button id="save" type="button" class="btn btn-primary">Save changes</button>';
								//print '<p hidden>'.$sales->ssid.'</td>';
								/*print '<p>Branch:'.$sales->bcode.' '.$sales->bname.'</p>';
								print '<p>Engine Number:'.$sales->engine_no.'</p>';
								print '<p>Customer Name:'.$sales->first_name.' '.$sales->last_name.'</p>';
								print '<td><a id="pid" class="btn btn-success" data-toggle="modal" data-target="#myModal">Edit</a></td>';
								print '</tr>'; */
							}
						}

							if (empty($table))
							{
								
								print '<p>No result.</p>';
							}
							?>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content"></div>
   </div>
   <div class="modal-dialog">
      <div class="modal-content"></div>
   </div>
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Edit Plate Number</h4>
         </div>
         <div class="modal-body"></div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button id="save" type="button" class="btn btn-primary">Save changes</button>
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

//Edit

/*$("#save").on("click", function(e) {
      e.preventDefault();
      var action=confirm('Are you sure you want to save?');
      if(action){
      $('#form_plateno').submit();
   }
});*/
</script>