<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.modal{
	position: fixed;
width: 60%;
top:10% !important;
left: 20%;
margin-top: auto; /* Negative half of height. */
margin-left: auto; /* Negative half of width. */
}
.tab-pane{
	border: 1px solid;
  border-color: #ddd #ddd #ddd #ddd;
	padding:20px;
}
.tabs-right>.nav-tabs {
    float: right;
    margin-left: 0px;
}
img{
	width: auto;height:250px;
}
</style>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Disapproved Repo Sales List</div>
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

      	<form id="da_form" method="post" class="form-horizontal" action="registration" target="_blank">
	        <input type="hidden" name="sid" value="0">

	        <table id="da_table" class="table">
	          <thead>
	            <tr>
	              <th><p>Date Sold</p></th>
	              <th><p>Branch</p></th>
	              <th><p>Customer Name</p></th>
	              <th><p>Engine #</p></th>
	              <th><p>Registration Type</p></th>
	              <th><p>AR #</p></th>
	              <th><p>Amount Given</p></th>
	              <th><p>Registration Expense</p></th>
	              <th><p>Topsheet</p></th>
	              <th><p>Reason for Disapprove</p></th>
                      <?php //if (in_array($this->session->position, [108])): ?>
	              <th></th>
                      <?php //endif; ?>
	            </tr>
	          </thead>
	          <tbody>
	          <?php
	          // $sales_type = array(0 => 'Brand New (Cash)', 1 => 'Brand New (Installment)');
	          // $post_sids = set_value('sid', array());
	          foreach ($table as $row)
	          {
							switch ($row->reg_type) {
								case 'RENEWAL':
										$registration = ($row->orcr_amt + $row->renewal_amt);
										break;
									case 'FOR TRANSFER':
										$registration = ($row->orcr_amt + $row->transfer_amt);
										break;
								default:
										$registration = ($row->orcr_amt + $row->renewal_amt + $row->transfer_amt);
									break;
							}
	            print '<tr id="tr_id_'.$row->repo_sales_id.'">';
	            print '<td>'.substr($row->date_sold, 0, 10).'</td>';
	            print '<td>'.$row->bcode.' '.$row->bname.'</td>';
	            print '<td>'.strtoupper($row->last_name.', '.$row->first_name.' '.$row->middle_name).'</td>';
	            print '<td>'.$row->engine_no.'</td>';
	            print '<td>'.$row->reg_type.'</td>';
	            print '<td>'.$row->ar_num.'</td>';
	            print '<td style="text-align:right">'.number_format($row->ar_amt,2).'</td>';
	            print '<td style="text-align:right">'.number_format($registration,2).'</td>';
	            print '<td>'.$row->trans_no.'</td>';
	            print '<td>'.$row->da.'</td>';
							print '<td><button value="'.$row->repo_sales_id.'" type="button" class="btn btn-success btn-edit-sales" data-title="Resolve Repo Sale - '.$row->da.'">Edit</button></td>';
              //       if (in_array($this->session->position, [108])) {
	            // print '<td><a class="btn btn-success" onclick="resolve('.$row->sid.')">Resolve</a></td>';
              //       }
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
