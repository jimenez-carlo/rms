<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Expense</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal">
          <?php print form_hidden('ltid', $ltid); ?>
          
          <table class="table">
            <thead>
              <tr>
                <th><p>Branch</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Engine #</p></th>
                <th><p>Sales Type</p></th>
                <th><p>Tip</p></th>
                <th><p>Registration</p></th>
                <th><p>Registered Date</p></th>
                <th><p>CR #</p></th>
                <th><p>MV File #</p></th>
                <th><p>Plate #</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $total = 0;
              foreach ($transmittal->sales as $sales)
              {
                $key = '['.$sales->sid.']';
                $expense = $registration[$sales->sid] - $sales->registration;
                $total += $expense;

                if ($expense == 0) {
                  $reg_exp = $sales->registration;
                }
                else if ($expense < 0) {
                  $reg_exp = $sales->registration.' <span style="color:green">('.$expense.')</span>';
                }
                else {
                  $reg_exp = $sales->registration.' <span style="color:red">(+'.$expense.')</span>';
                }

                $tip_exp = '0.00';
                if ($tip[$sales->sid] > 0) {
                  $total += $tip[$sales->sid];
                  $tip_exp .= ' <span style="color:red">(+'.$tip[$sales->sid].')</span>';

                }

                print '<tr>';
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td>'.$sales->sales_type.'</td>';
                print '<td>'.form_hidden('tip'.$key, $tip[$sales->sid]).$tip_exp.'</td>';
                print '<td>'.form_hidden('registration'.$key, $registration[$sales->sid]).$reg_exp.'</td>';
                print '<td>'.form_hidden('cr_date'.$key, $cr_date[$sales->sid]).$cr_date[$sales->sid].'</td>';
                print '<td>'.form_hidden('cr_no'.$key, $cr_no[$sales->sid]).$cr_no[$sales->sid].'</td>';
                print '<td>'.form_hidden('mvf_no'.$key, $mvf_no[$sales->sid]).$mvf_no[$sales->sid].'</td>';
                print '<td>'.form_hidden('plate_no'.$key, $plate_no[$sales->sid]).$plate_no[$sales->sid].'</td>';
                print '</tr>';
              }
              ?>
            </tbody>
          </table>

          <hr>
          <fieldset>
            <div class="span1"></div>
            <div class="span2">
              <div class="control-group">
                <div class="control-label">Cash Fund</div>
                <div class="controls text"><?php print number_format($transmittal->cash, 2, '.', ','); ?></div>
              </div>
              <div class="control-group">
                <div class="control-label">Total Change Expense</div>
                <div class="controls text">
                  <?php
                  $total = $total * -1;
                  print form_hidden('expense', $total);

                  if ($total < 0) {
                    print '<span style="color:red">'.number_format($total, 2, '.', ',').'</span>';
                  }
                  else if ($total > 0) {
                    print '<span style="color:green">+'.number_format($total, 2, '.', ',').'</span>';
                  }
                  else {
                    print number_format($total, 2, '.', ',');
                  }
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label"><b>Cash Balance</b></div>
                <div class="controls text">
                  <?php
                  $balance = $transmittal->cash + $total;
                  if ($balance < 0) {
                    print '<span style="color:red"><b>'.number_format($balance, 2, '.', ',').'</b></span>';
                    $disabled = 'disabled';
                  }
                  else {
                    print '<b>'.number_format($balance, 2, '.', ',').'</b>';
                    $disabled = '';
                  }
                  ?>
                </div>
              </div>
            </div>
            <div class="span1"></div>
            <div class="span8">
              <div class="control-group">
                <div class="controls text">
                  <?php
                  if ($balance < 0) {
                    print '<b><p style="color:red">Cash fund is not enough to accomodate total expense.</p></b>';
                  }
                  else {
                    print '<b><p>The following records will be tagged as Registered and cannot be undone. Continue?</p></b>';
                  }
                  ?>
                </div>
              </div>
              <div class="form-actions">
                <input type="submit" name="submit_all" value="Submit" class="btn btn-success" <?php print $disabled; ?>>
                <input type="submit" name="back" value="Back" class="btn btn-success">
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>