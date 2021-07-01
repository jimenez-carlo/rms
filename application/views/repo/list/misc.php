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
          <div class="pull-left">Disapproved Miscellaneous List</div>
      </div>
      <div class="block-content collapse in">
        <?php if(!in_array($this->session->position_name, array('Branch Secretary', 'Branch Head1', 'Cash Custodian'))): ?>
      	<form class="form-horizontal" method="post">
      		<div class="control-group span5">
      			<div class="control-label">Branch</div>
      			<div class="controls">
      				<?php print form_dropdown('branch', array(0 => '- Please select a branch -') + $branch, set_value('branch')); ?>
      			</div>
      		</div>

      		<div class="form-actions">
      			<input type="submit" name="search" value="Search" class="btn btn-success">
      		</div>
      	</form>
        <?php endif; ?>

      	<hr>

      	<form id="da_form" method="post" class="form-horizontal" action="return_fund_view" target="_blank">
	        <input type="hidden" name="sid" value="0">

	        <table id="da_table" class="table">
	          <thead>
	            <tr>
                <th><p>Batch</p></th>
	              <th><p>Branch</p></th>
	              <th><p>Region</p></th>
	              <th><p>Date</p></th>
	              <th><p>OR No#</p></th>
	              <th><p>Amount</p></th>
	              <th><p>Expense Type</p></th>
                <th></th>
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
	            print '<td>'.$row->type.'</td>';
              print '<td><button value="'.$row->mid.'" type="button" class="btn btn-success btn-edit-misc" data-title="Edit Repo Miscellaneous">Edit</button></td>';
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
	      </form>
      </div>
    </div>
  </div>
</div>
	</div>