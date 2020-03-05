<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<form class="form-horizontal" method="post" style="margin:0;">
  <?php print form_hidden('vid', $ca_ref['reference']); ?>

  <fieldset>
    <div class="control-group span4">
      <div class="control-label">Date</div>
      <div class="controls"><?php print $ca_ref['date']; ?></div>
    </div>
    <div class="control-group span4">
      <div class="control-label">Region</div>
      <div class="controls"><?php print $ca_ref['region']; ?></div>
    </div>
    <div class="control-group span4">
      <div class="control-label">Company</div>
      <div class="controls"><?php print $ca_ref['company']; ?></div>
    </div>
  </fieldset>

  <hr>
  <table class="table tbl-sales" style="margin:0;">
    <thead>
      <tr>
        <th><p>Branch</p></th>
        <th width=75><p>Date Sold</p></th>
        <th width=125><p>Engine #</p></th>
        <th><p>Type of Sales</p></th>
        <th><p>Registration Type</p></th>
        <th><p>AR #</p></th>
        <th><p class="text-right">Amount Given</p></th>
        <th><p class="text-right">LTO Registration</p></th>
        <th><p class="text-right">Total Expense</p></th>
        <th><p class="text-right">Balance</p></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total_amt = 0;
      $total_exp = 0;

      foreach (json_decode($ca_ref['sales']) as $sales)
      {
        print '<tr class="sales-'.$sales->sid.'" onclick="attachment('.$sales->sid.', 1)">';
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
        print '<td><p class="text-right sales-exp">'.number_format($sales->registration, 2, ".", ",").'</p></td>';
        print '<td><p class="text-right">'.number_format($sales->amount - $sales->registration, 2, ".", ",").'</p></td>';
        print '</tr>';
        $total_amt += $sales->amount;
        $total_exp += $sales->registration;
      }

      // Miscellaneous
      print '<tr style="border-top: double">';
      print '<th colspan="3"><p>OR #</p></th>';
      print '<th colspan="2"><p>OR Date</p></th>';
      print '<th colspan="2"><p class="text-right">Type</p></th>';
      print '<th colspan="3"><p class="text-right">Expense</p></th>';
      print '</tr>';

      foreach (json_decode($ca_ref['misc_expense']) as $misc)
      {
        print '<tr class="misc-'.$misc->mid.'" onclick="attachment('.$misc->mid.', 2)">';
        print '<td colspan="3">';
        print '<input type="hidden" name="mid[]" value="'.$misc->mid.'">';
        print $misc->or_no;
        print '</td>';

        print '<td colspan="2">'.$misc->or_date.'</td>';
        print '<td colspan="2"><p class="text-right">'.$misc->type.'</p></td>';
        print '<td colspan="3"><p class="text-right misc-exp">'.$misc->amount.'</p></td>';
        print '</tr>';
        $total_exp += $misc->amount;
      }

      if (empty($ca_ref['misc_expense']))
      {
        print '<tr>';
        print '<td colspan="3"><p style="color:red"><b>No included miscellaneous expense.</b></p></td>';
        print '<td colspan="2"></td>';
        print '<td colspan="2"></td>';
        print '<td colspan="3"></td>';
        print '</tr>';
      }
      ?>
    </tbody>
    <tfoot style="border-top: dotted gray; font-size: 16px">
      <tr>
        <th colspan="8"></th>
        <th><p class="text-right">Total Amount</p></th>
        <th><p class="text-right">&#x20b1 <?php print number_format($total_amt, 2, ".", ","); ?></p></th>
      </tr>
      <tr>
        <th colspan="8">
          <p>Please make sure all information are correct before proceeding.</p>
        </th>
        <th><p class="text-right">Total Expense</p></th>
        <th><p class="text-right">&#x20b1 <?php print number_format($total_exp, 2, ".", ","); ?></p></th>
      </tr>
      <tr>
        <th colspan="8">
          <input type="submit" name="submit_all" value="Submit" class="btn btn-success">
          <input type="submit" name="back" value="Back" class="btn btn-success">
        </th>
        <th><p class="text-right">Balance</p></th>
        <th><p class="text-right">&#x20b1 <?php print number_format($total_amt - $total_exp, 2, ".", ","); ?></p></th>
      </tr>
    </tfoot>
  </table>
</form>
