<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Voucher</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <fieldset>
            <div class="control-group span6">
              <div class="control-label">Voucher Date</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_to', set_value('date_from', date('Y-m-d')), array('class' => 'datepicker')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker')); ?>
              </div>
            </div>
            <div class="span4">
              <input type="submit" class="btn btn-success" value="Apply">
            </div>
          </fieldset>
        </form>

        <table class="table">
          <thead>
            <tr>
              <th><p>Voucher #</p></th>
              <th><p>Voucher Date</p></th>
              <th style="text-align:right;padding-right:10px;"><p>Amount</p></th>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th><p>Debit Memo #</p></th>
              <th><p>Transferred Date</p></th>
              <th><p>Status</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->voucher_no.'</td>';
              print '<td>'.$row->date.'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->amount,2,'.',',').'</td>';
              print '<td>'.$row->dm_no.'</td>';
              print '<td>'.$row->region.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td>'.$row->transfer_date.'</td>';
              print '<td>'.$row->status.'</td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr><td colspan=20>No result.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>