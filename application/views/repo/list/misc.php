<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.modal{
	position: fixed;
width: 50%;
top:10% !important;
left: 25%;
margin-top: auto; /* Negative half of height. */
margin-left: auto; /* Negative half of width. */
}
</style>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left"><?php echo (in_array($this->session->position, array(72,73,83))) ? 'Disapproved': '';?> Miscellaneous List</div>
      </div>
      <div class="block-content collapse in">
        <?php if(!in_array($this->session->position_name, array('Branch Secretary', 'Branch Head1', 'Cash Custodian'))): ?>
      	<form class="form-horizontal" method="post">
					
					<div class="control-group span5">
            <label class="control-label">Branch</label>
            <div class="controls">
						<?php print form_dropdown('branch', array(0 => '- Please select a branch -') + $branch, set_value('branch')); ?>
            </div>
          </div>

					<?php $type = array('all' => '- Any -') + $type; ?>
					<div class="control-group span5">
            <label class="control-label">Type</label>
            <div class="controls">
						<?php echo form_dropdown('type', $type, set_value('type')); ?>
            </div>
          </div>

					<div class="control-group span5">
            <label class="control-label"></label>
            <div class="controls">
						<input type="submit" name="search" value="Search" class="btn btn-success ">
            </div>
          </div>


					<?php $status = array('all' => '- Any -') + $status; ?>
					<div class="control-group span5">
            <label class="control-label">Status</label>
            <div class="controls">
						<?php  echo form_dropdown('status', $status, set_value('status', $default_status)); ?>
            </div>
          </div>

      	</form>
        <?php endif; ?>

      	<hr>

	        <input type="hidden" name="sid" value="0">

	        <table id="tbl_exp" class="table">
	          <thead>
	            <tr>
                <th><p>Batch</p></th>
	              <th><p>Branch</p></th>
	              <th><p>Region</p></th>
	              <th><p>Date</p></th>
	              <th><p>OR No#</p></th>
	              <th><p>Amount</p></th>
	              <th><p>Expense Type</p></th>
                <?php echo (in_array($this->session->position, array(72,73,83))) ? '<th></th>': '<th>Status</th>';?>
                <?php //if (in_array($this->session->position, [108])): ?>
	            </tr>
	          </thead>
	          <tbody>
	          <?php
	          $sales_type = array(0 => 'Brand New (Cash)', 1 => 'Brand New (Installment)');
	          $post_sids = set_value('sid', array());
	          foreach ($table as $row)
	          {
	            print '<tr id="tr_id_'.$row->mid.'">';
	            print '<td>'.$row->reference.'</td>';
	            print '<td>'.$row->branch.'</td>';
	            print '<td>'.$row->region.'</td>';
	            print '<td>'.$row->date.'</td>';
	            print '<td>'.$row->or_no.'</td>';
	            print '<td style="text-align:right">'.number_format($row->amount,2).'</td>';
	            print '<td>'.strtoupper($row->type).'</td>';
							print (in_array($this->session->position, array(72,73,83))) ? '<td><button value="'.$row->mid.'" type="button" class="btn btn-success btn-edit-misc" data-title="Edit Repo Miscellaneous">Edit</button></td>': '<td>'.strtoupper($row->status_name).'</td>';
	            print '</tr>';
	          }
	          if (empty($table))
	          {
	            print '<tr>
	              <td colspan=20>No result.</td>
	            </tr>';
	          }
	          ?>
	          </tbody>
	        </table>
      </div>
    </div>
  </div>
</div>
	</div>
	<script>
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
								</script>