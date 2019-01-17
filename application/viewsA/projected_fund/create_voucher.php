<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<p>Please select the projected fund to be included in the voucher.</p>
<p>Click <a onclick="print()">here</a> to print selected projected funds.</p>
<form id="print" action="projected_fund/sprint/<?php print $fund->fid; ?>" method="post" target="_blank"></form>

<form action="#" id="form" class="form-horizontal" style="margin:0px!important;">
  <table class="table projected" style="margin:0px!important;">
    <thead>
      <tr>
        <th width="20"></th>
        <th width="100">Transmittal Date</th>
        <th width="100" style="text-align:right;padding-right:10px;">Amount</th>
        <th style="text-align:right;padding-right:10px;">No. of Cash Units</th>
        <th style="text-align:right;padding-right:10px;">No. of Installment Units</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      foreach ($fund->projected as $row)
      {
        $total += $row->amount;
        print '<tr>';
        print '<td><input type="checkbox" name="fpid['.$row->fpid.']" value="'.$row->amount.'" onchange="total()" class="amount" checked></td>';
        print '<td>'.$row->date.'</td>';
        print '<td style="text-align:right;padding-right:10px;">'.number_format($row->amount,2,'.',',').'</td>';
        print '<td style="text-align:right;padding-right:10px;">'.number_format($row->unit_cash,0,'.',',').'</td>';
        print '<td style="text-align:right;padding-right:10px;">'.number_format($row->unit_inst,0,'.',',').'</td>';
        print '</tr>';
      }
      ?>
    </tbody>
  </table>

  <hr style="margin:10px!important;">
  <div class="control-group">
    <label class="control-label">Total Amount</label>
    <div class="controls text">
      <span id="total-projected">&#x20b1 <?php print number_format($total, 2, '.', ','); ?></span>
      <?php print form_input('amount', set_value('amount', $total), array('class' => 'hide')); ?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">Voucher #</label>
    <div class="controls">
      <input type="text" name="voucher_no">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">Debit Memo #</label>
    <div class="controls">
      <input type="text" name="dm_no">
    </div>
  </div>
</form>