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
.block-content {
  margin: 2em;
}
    .block{
           border:unset;
      /* border-top: 1px solid #f5f5f5; */
    }
</style>
    <!-- block -->
    <hr>

      	<form id="da_form" method="post" class="form-horizontal" action="registration" target="_blank">

	        <table id="data-table" class="table">
	          <thead>
	            <tr>
	              <th><p>Branch Code</p></th>
	              <th><p>Branch Name</p></th>
	              <th><p>Renewal</p></th>
	              <th><p>Transfer</p></th>
	              <th><p>Hpg Pnp Clearance</p></th>
	              <th><p>Insurance</p></th>
	              <th><p>Emission</p></th>
	              <th><p>Unreceipted Renewal</p></th>
	              <th><p>Unreceipted Transfer</p></th>
	              <th><p>Unreceipted Macro Etching</p></th>
                <th><p>Unreceipted Hpg Pnp Clearance</p></th>
                <th><p>Unreceipted Plate</p></th>
	              <th></th>
	            </tr>
	          </thead>
	          <tbody>
	          <?php
	          // $sales_type = array(0 => 'Brand New (Cash)', 1 => 'Brand New (Installment)');
	          // $post_sids = set_value('sid', array());
	          foreach ($table as $res)
	          {
	            print '<tr id="tr_id_'.$res['bcode'].'">';
	            print '<td>'.$res['bcode'].'</td>';
	            print '<td>'.$res['name'].'</td>';
	            print '<td style="text-align:right">'.number_format($res['sop_renewal'],2).'</td>';
	            print '<td style="text-align:right">'.number_format($res['sop_transfer'],2).'</td>';
	            print '<td style="text-align:right">'.number_format($res['sop_hpg_pnp_clearance'],2).'</td>';
	            print '<td style="text-align:right">'.number_format($res['insurance'],2).'</td>';
	            print '<td style="text-align:right">'.number_format($res['emission'],2).'</td>';
	            print '<td style="text-align:right">'.number_format($res['unreceipted_renewal_tip'],2).'</td>';
	            print '<td style="text-align:right">'.number_format($res['unreceipted_transfer_tip'],2).'</td>';
	            print '<td style="text-align:right">'.number_format($res['unreceipted_macro_etching_tip'],2).'</td>';
              print '<td style="text-align:right">'.number_format($res['unreceipted_hpg_pnp_clearance_tip'],2).'</td>';
              print '<td style="text-align:right">'.number_format($res['unreceipted_plate_tip'],2).'</td>';
							print '<td><button value="'.$res['bcode'].'" type="button" class="btn btn-success btn-edit-branch-tip" data-title="Edit Branch Code - '.$res['bcode'].'">Edit</button></td>';
	            print '</tr>';
	          }

	          ?>
	          </tbody>
	        </table>
	      </form>