<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Disapprove List</div>
      </div>
      <div class="block-content collapse in">
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

      	<hr>

      	<form id="da_form" method="post" class="form-horizontal" action="registration" target="_blank">
	        <input type="hidden" name="sid" value="0">

	        <table id="da_table" class="table">
	          <thead>
	            <tr>
	              <th><p>Date Sold</p></th>
	              <th><p>Branch</p></th>
	              <th><p>Customer Name</p></th>
	              <th><p>Engine #</p></th>
	              <th><p>SI #</p></th>
	              <th><p>Registration Type</p></th>
	              <th><p>AR #</p></th>
	              <th><p>Registration Expense</p></th>
	              <th><p>CR #</p></th>
	              <th><p>Topsheet</p></th>
	              <th><p>Reason for Disapprove</p></th>
	              <th><p></p></th>
	            </tr>
	          </thead>
	          <tbody>
	          <?php
	          $sales_type = array(0 => 'Brand New (Cash)', 1 => 'Brand New (Installment)');
	          $post_sids = set_value('sid', array());
	          foreach ($table as $row)
	          {
	            print '<tr>';
	            print '<td>'.substr($row->date_sold, 0, 10).'</td>';
	            print '<td>'.$row->bcode.' '.$row->bname.'</td>';
	            print '<td>'.$row->first_name.' '.$row->last_name.'</td>';
	            print '<td>'.$row->engine_no.'</td>';
	            print '<td>'.$row->si_no.'</td>';
	            print '<td>'.$row->registration_type.'</td>';
	            print '<td>'.$row->ar_no.'</td>';
	            print '<td>'.$row->registration.'</td>';
	            print '<td>'.$row->cr_no.'</td>';
	            print '<td>'.$row->trans_no.'</td>';
	            print '<td>'.$da_reason[$row->da_reason].'</td>';
	            print '<td><a class="btn btn-success" onclick="resolve('.$row->sid.')">Resolve</a></td>';
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

<script type="text/javascript">
  function resolve(sid) {
    $('#da_form input[name=sid]').val(sid);
    $('#da_form').submit();
  }
</script>