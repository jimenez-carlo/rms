<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">
					Self Registration Report
				</div>
			</div>
			<div class="block-content collapse in">
			
				<form class="form-horizontal" method="post" style="margin:10px 0px">
					<fieldset>
          
	          <div class="control-group span4" style="margin-bottom:0;">
	            <div class="control-label">Date Sold</div>
	            <div class="controls">
              <input type="text" name="date_sold" class="datepicker" value="<?php if(isset($_POST['date_sold'])) print $_POST['date_sold']; ?>">
	            </div>
	          </div>
          
	          <div class="control-group span4" style="margin-bottom:0;">
	            <div class="control-label">Transmittal Status</div>
	            <div class="controls">
	              <select name="type">
	                <option value="with" <?php if(isset($_POST['type']) && $_POST['type'] == 'with') echo 'selected'; ?>>With Transmittal</option>
	                <option value="without" <?php if(isset($_POST['type']) && $_POST['type'] == 'without') echo 'selected'; ?>>Without Transmittal</option>
	              </select>
	            </div>
	          </div>
            <input type="submit" name="search" value="Search" class="btn btn-success">
					</fieldset>
				</form>

				<?php if(!empty($sales)) { ?>
				<table class="table" style="margin:0">
					<thead>
						<tr>
							<th>Date Sold</th>
							<th>Branch</th>
							<th>Engine #</th>
							<th>Customer Name</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($sales as $key => $sale) {
							print '<tr>';
							print '<td>'.substr($sale->date_sold, 0, 10).'</td>';
							print '<td>'.$sale->branch->b_code.' '.$sale->branch->name.'</td>';
							print '<td>'.$sale->engine_no.'</td>';
							print '<td>'.$sale->first_name.' '.$sale->last_name.'</td>';
							print '<td>'.$sale->status.'</td>';
							print '</tr>';
						}
						?>
					</tbody>
				</table>
				<?php } ?>

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
      <h3 class="modal-title">Hold</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <form action="#" id="form" class="form-horizontal">
        <div class="form-body">
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label" style="margin-right:10px;">Reason</label>
            <div class="controls"><textarea name="reason"></textarea></div>
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnHold" onclick="hold_sr()" class="btn btn-success">Hold</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->