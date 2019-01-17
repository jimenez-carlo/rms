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
						<div class="control-group span4" style="margin:0;">
							<?php
								echo form_label('Check #', 'check_no', array('class' => 'control-label'));
								echo '<div class="controls">';
								echo form_input('check_no', set_value('check_no'));
								echo '</div>';
							?>
						</div>
          
	          <div class="control-group span4" style="margin-bottom:0;">
	            <div class="control-label">Company</div>
	            <div class="controls">
	              <select name="company">
	                <option value=1 <?php if(isset($_POST['company']) && $_POST['company'] == 1) echo 'selected'; ?>>MNC</option>
	                <option value=2 <?php if(isset($_POST['company']) && $_POST['company'] == 2) echo 'selected'; ?>>MTI</option>
	                <option value=3 <?php if(isset($_POST['company']) && $_POST['company'] == 3) echo 'selected'; ?>>HPTI</option>
	              </select>
	            </div>
	          </div>
            <input type="submit" name="search" value="Search" class="btn btn-success">
					</fieldset>

				<?php if(!empty($checks)) { ?>
					<table class="table" style="margin:0">
						<thead>
							<tr>
								<th>Check Date</th>
								<th>Check Number</th>
								<th>Region</th>
								<th>Company</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
	            <?php
	            foreach ($checks as $check)
	            {
	              print '<tr>';
	              print '<td>'.$check->check_date.'</td>';
	              print '<td>'.$check->check_no.'</td>';
	              print '<td>'.$check->region.'</td>';
	              print '<td>'.$check->company.'</td>';
	              print '<td>';
	              if($check->hold) print '<input type="submit" name="lto['.$check->cid.']" value="Unhold" class="btn btn-success">';
	              else print '<input type="button" name="hold" value="Hold" class="btn btn-success" onclick="hold_c('.$check->cid.')">';
	              print '</td>';
	              print '</tr>';
	            }

	            if (empty($checks))
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
        <button type="button" id="btnHold" onclick="hold_check()" class="btn btn-success">Hold</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->