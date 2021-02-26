<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<form class="form-horizontal" method="post" style="margin:0;">
  <?php
    print form_hidden('vid', $batch_ref['reference']);
    print form_hidden('region', $batch_ref['region_initial']);
    print form_hidden('company', $batch_ref['company']);
  ?>

  <fieldset>
    <div class="control-group span4">
      <div class="control-label">Date</div>
      <div class="controls"><?php print $batch_ref['date']; ?></div>
    </div>
    <div class="control-group span4">
      <div class="control-label">Region</div>
      <div class="controls"><?php print $batch_ref['region']; ?></div>
    </div>
    <div class="control-group span4">
      <div class="control-label">Company</div>
      <div class="controls"><?php print $batch_ref['company']; ?></div>
    </div>
  </fieldset>

  <hr>
  <table class="table tbl-sales" style="margin:0;">
    <?php $total_exp = 0; ?>
    <?php if (isset($batch_ref['sales'])): ?>
    <thead>
      <tr>
        <th><p>#</p></th>
        <th><p>Branch</p></th>
        <th width=75><p>Date Sold</p></th>
        <th width=125><p>Engine #</p></th>
        <th><p>Type of Sales</p></th>
        <th><p>Registration Type</p></th>
        <th><p>AR #</p></th>
        <th><p class="text-right">Amount Given</p></th>
        <th><p class="text-right">LTO Registration</p></th>
        <th><p class="text-right">Penalty</p></th>
        <th><p class="text-right">Total Expense</p></th>
        <th><p class="text-right">Balance</p></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total_amt = 0;
      $row_count = 1;

      foreach (json_decode($batch_ref['sales']) as $sales)
      {
        print '<tr class="sales-'.$sales->sid.'" onclick="attachment('.$sales->sid.', 1)">';
        print '<td>'.$row_count.'</td>';
        $row_count++;
        print '<td>';
        print '<input type="hidden" name="sid[]" value="'.$sales->sid.'">';
        print $sales->bcode.' '.$sales->bname;
        print '</td>';

        print '<td>'.$sales->date_sold.'</td>';
        print '<td>'.$sales->engine_no.'</td>';
        print '<td>'.$sales->sales_type.'</td>';
        print '<td>'.$sales->registration_type.'</td>';
        print '<td>'.$sales->ar_no.'</td>';
        print '<td><p class="text-right sales-amt">'.number_format($sales->amount, 2, ".", ",").'</p></td>';
        print '<td><p class="text-right">'.number_format($sales->registration, 2, ".", ",").'</p></td>';
        print '<td><p class="text-right">'.number_format($sales->penalty, 2, ".", ",").'</p></td>';
        print '<td><p class="text-right sales-exp">'.number_format($sales->registration+$sales->penalty, 2, ".", ",").'</p></td>';
        print '<td><p class="text-right">'.number_format($sales->amount - $sales->registration, 2, ".", ",").'</p></td>';
        print '</tr>';
        $total_amt += $sales->amount;
        $total_exp += $sales->registration;
      }
      ?>
    <?php endif; ?>
      <?php
      // Miscellaneous
      print '<tr style="border-top: double">';
      print '<th colspan="2"><p>#</p></th>';
      print '<th colspan="3"><p>OR #</p></th>';
      print '<th colspan="2"><p>OR Date</p></th>';
      print '<th colspan="2"><p class="text-right">Type</p></th>';
      print '<th colspan="3"><p class="text-right">Expense</p></th>';
      print '</tr>';

      if (empty($batch_ref['misc_expense'])) {
        print '<tr>';
        print '<td colspan="2"></td>';
        print '<td colspan="3"><p style="color:red"><b>No miscellaneous expense included.</b></p></td>';
        print '<td colspan="2"></td>';
        print '<td colspan="2"></td>';
        print '<td colspan="3"></td>';
        print '</tr>';
      } else {
        $misc_expenses = json_decode($batch_ref['misc_expense']);
        $exp_row_count = 1;
        foreach ($misc_expenses as $misc)
        {
          print '<tr class="misc-'.$misc->mid.'" onclick="attachment('.$misc->mid.', 2)">';
          print '<td colspan="2">'.$exp_row_count.'</td>';
          print '<td colspan="3">';
          print '<input type="hidden" name="mid[]" value="'.$misc->mid.'">';
          print $misc->or_no;
          print '</td>';

          print '<td colspan="2">'.$misc->or_date.'</td>';
          print '<td colspan="2"><p class="text-right">'.$misc->type.'</p></td>';
          print '<td colspan="3"><p class="text-right misc-exp">'.$misc->amount.'</p></td>';
          print '</tr>';
          $exp_row_count++;
          $total_exp += $misc->amount;
        }
      }
      ?>
    </tbody>
    <tfoot style="border-top: dotted gray; font-size: 16px">
      <tr>
        <th colspan="10"></th>
        <th><p>Total Amount</p></th>
      </tr>
      <tr>
        <th colspan="9"></th>
        <th><p class="text-right">Batch</p></th>
        <th><p class="text-right tot-amt">&#x20b1 <?php print number_format($batch_ref['amount'], 2, ".", ","); ?></p></th>
      </tr>
      <tr>
        <th colspan="9"><p>Please make sure all information are correct before proceeding.</p></th>
        <th><p class="text-right">Expense</p></th>
        <th><p class="text-right tot-amt">&#x20b1 <?php print number_format($total_exp, 2, ".", ","); ?></p></th>
      </tr>
      <tr>
        <th colspan="9">
          <input type="submit" name="submit_all" value="Submit" class="btn btn-success">
          <input type="submit" name="back" value="Back" class="btn btn-success">
        </th>
        <th><p class="text-right">Balance</p></th>
        <th><p id="balance" class="text-right tot-bal">&#x20b1 <?php print number_format($batch_ref['amount'] - ($total_exp + $batch_ref['liquidated'] + $batch_ref['checked']), 2, ".", ","); ?></p></th>
      </tr>
    </tfoot>
  </table>
</form>
