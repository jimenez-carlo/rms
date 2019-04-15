<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">
					Expense
				</div>
			</div>
			<div class="block-content collapse in">
			
				<form class="form-horizontal" method="post" style="margin:10px 0px">
					<fieldset>
						<div class="control-group span4" style="margin:0;">
							<?php
								echo form_label('OR #', 'or_no', array('class' => 'control-label'));
								echo '<div class="controls">';
								echo form_input('or_no', set_value('or_no'));
								echo '</div>';
							?>
						</div>
          
	          <div class="control-group span4" style="margin-bottom:0;">
	            <div class="control-label">OR Date</div>
	            <div class="controls">
              <input type="text" name="or_date" class="datepicker" value="<?php if(isset($_POST['or_date'])) print $_POST['or_date']; ?>">
	            </div>
	          </div>
            <input type="submit" name="search" value="Search" class="btn btn-success" style="margin-right:10px;"> 
					</fieldset>

				<?php if(!empty($miscs)) { ?>
					<table class="table" style="margin:10px 0px 0px 0px">
						<thead>
							<tr>
								<th>OR Number</th>
								<th>OR Date</th>
								<th>Amount</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
	            <?php
	            foreach ($miscs as $misc)
	            {
	              print '<tr>';
	              print '<td>'.$misc->or_no.'</td>';
	              print '<td>'.$misc->or_date.'</td>';
	              print '<td>'.$misc->amount.'</td>';
	              print '<td>'.$misc->status.'</td>';
	              print '<td><a href="./../expense/view/'.$misc->mid.'" class="btn btn-success">View</a></td>';
	              print '</tr>';
	            }
	            ?>
						</tbody>
					</table>
				</form>
				<?php } else print '<p>No results found.</p>'; ?>

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