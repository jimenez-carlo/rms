<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<form id="print" action="projected_fund/sprint" method="post" target="_blank">
  <?php print form_hidden('fid', $fund->fid); ?>
  <div class="container"></div>
</form>

<form action="#" id="form" class="form-horizontal" style="margin:0px!important;">
  <?php print form_hidden('reference', $fund->reference); ?>

  <table class="table projected" style="margin:0px!important;">
    <thead>
      <tr>
        <th width="20"></th>
        <th width="125">Company</th>
        <th width="125">Transmittal #</th>
        <th width="100">Transmittal Date</th>
        <th width="75" style="text-align:right;padding-right:10px;">Amount</th>
        <th width="50" style="text-align:right;padding-right:10px;">Units</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $total = 0;
        foreach ($fund->transmittal as $row) {
          $total += $row->amount;
          print '<tr>';
          print '<td><input type="checkbox" name="ltid['.$row->ltid.']" value="'.$row->amount.'" onchange="total()" class="amount" checked></td>';
          print '<td>'.$company[$row->company].'</td>';
          print '<td>'.$row->code.'</td>';
          print '<td>'.$row->date.'</td>';
          print '<td style="text-align:right; padding-right:10px;">'.number_format($row->amount,2,'.',',').'</td>';
          print '<td style="text-align:right; padding-right:10px;">'.number_format($row->sales,0,'.',',').'</td>';
          print '</tr>';
        }
      ?>
    </tbody>
  </table>

  <hr style="margin:10px!important;">

  <div class="row-fluid">
    <div class="span1"></div>
    <div class="span5">
      <p>Please select the projected fund to be included.</p>
      <p>Click <a onclick="print()">here</a> to print selected projected funds.</p>
    </div>
    <div class="span5">
      <div class="control-group">
        <label class="control-label">Total Amount</label>
        <div class="controls text">
          <span id="total-projected">&#x20b1 <?php print number_format($total, 2, '.', ','); ?></span>
          <?php print form_input('amount', set_value('amount', $total), array('class' => 'hide')); ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label">Document #</label>
        <div class="controls">
          <input type="text" name="voucher_no">
        </div>
      </div>
    </div>
  </div>
</form>
