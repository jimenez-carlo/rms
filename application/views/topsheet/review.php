<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Topsheet # <?php print $topsheet->trans_no; ?></div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post" style="margin:10px 0px;">
          <?php
          print form_hidden('tid', $topsheet->tid);
          ?>

      		<table class="table">
    				<thead>
    					<tr>
    						<th style="width: 30%"><p>Branch</p></th>
    						<th style="width: 30%"><p>Expense Date</p></th>
    						<th style="width: 15%"><p class="text-right">Given Amount</p></th>
    						<th><p class="text-right">Expense</p></th>
    					</tr>
    				</thead>
    				<tbody>
    					<?php
              $total = 0;

              foreach ($topsheet->sales as $sales)
              {
                print '<tr>';
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$sales->date.'</td>';
                print '<td><p class="text-right">'.number_format($sales->amount, 2, ".", ",").'</p></td>';
                print '<td><p class="text-right">'.number_format($sales->expense, 2, ".", ",").'</p></td>';
                print '</tr>';

                $total += $sales->expense;
              }

              // Miscellaneous
              print '<tr style="border-top: double">';
              print '<th><p>OR #</p></th>';
              print '<th><p>OR Date</p></th>';
              print '<th><p class="text-right">Type</p></th>';
              print '<th><p class="text-right">Expense</p></th>';
              print '</tr>'; 
              foreach ($miscs as $misc)
              {
                print form_hidden('mid['.$misc->mid.']', $misc->mid);
                print '<tr>';
                print '<td>'.$misc->or_no.'</td>';
                print '<td>'.$misc->or_date.'</td>';
                print '<td><p class="text-right">'.$misc->type.'</p></td>';
                print '<td><p class="text-right">'.$misc->amount.'</p></td>';
                print '</tr>';
                $total += $misc->amount;
              }
              if (empty($miscs))
              {
                print '<tr>';
                print '<td><p style="color:red"><b>No included miscellaneous expense.</b></p></td>';
                print '<td></td>';
                print '<td></td>';
                print '<td></td>';
                print '</tr>';
              }
              ?>

    				</tbody>
    				<tfoot style="border-top: dotted gray; font-size: 16px">
              <tr>
                <th colspan="2">Click <a class="print">here</a> to print this summary</th>
                <th><p class="text-right">Total Amount</p></th>
                <th><p class="text-right exp">&#x20b1 <?php print number_format($topsheet->total_credit, 2, ".", ","); ?></p></th>
              </tr>
              <tr>
                <th colspan="2">Please make sure that all information are correct before proceeding.</th>
                <th><p class="text-right">Total Expense</p></th>
                <th><p class="text-right exp">&#x20b1 <?php print number_format($total, 2, ".", ","); ?></p></th>
              </tr>
              <tr>
                <th colspan="2">
                  <input type="submit" name="submit_all" value="Submit" class="btn btn-success">
                  <input type="submit" name="back[1]" value="Back" class="btn btn-success">
                </th>
                <th><p class="text-right">Balance</p></th>
                <th><p class="text-right exp">&#x20b1 <?php print number_format($topsheet->total_credit - $total, 2, ".", ","); ?></p></th>
              </tr>
    				</tfoot>
      		</table>
        </form>
  	  </div>
  	</div>
  </div>
</div>

<form id="print_form" method="post" action="sprint" target="_blank">
  <?php
  print form_hidden('tid', $topsheet->tid);
  foreach ($miscs as $misc) {
    print form_hidden('mid['.$misc->mid.']', $misc->mid);
  }
  ?>
</form>

<script type="text/javascript">
$(function(){
  $(document).ready(function(){
    $('.print').click(function(){
      $('#print_form').submit();
    });
  });
});
</script>