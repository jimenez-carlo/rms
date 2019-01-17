<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">

    <!-- MNC -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">MNC</div>
      </div>
      <div class="block-content collapse in">
        <table class="table" style="margin:0px;">
          <thead>
            <tr>
              <th><p>Transaction Date</p></th>
              <th><p>Transfered</p></th>
              <th><p>Withdrawal</p></th>
              <th><p>Deposited</p></th>
              <th><p>Cash In Bank Balance</p></th>
              <th><p>Cash on Hand Balance</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($mnc as $row) 
            {
              print '<tr>';
              print '<td>'.$row->date.'</td>';
              switch ($row->type)
              {
/*    1 => 'Transfer',
    2 => 'Cash Withdrawal',
    3 => 'Check Withdrawal',
    4 => 'Deposit',
    5 => 'NRU',
    6 => 'Registration',
    7 => 'Miscellaneous*/
                case 1:
                  print '<td style="text-align:right !important;padding-right:10xpx;">'.number_format($row->in_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  print '<td></td>';
                  break;
                case 2:
                  print '<td></td>';
                  print '<td style="text-align:right !important;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 3:
                  print '<td></td>';
                  print '<td style="text-align:right !important;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 4:
                  print '<td></td>';
                  print '<td></td>';
                  print '<td style="text-align:right !important;padding-right:10xpx;">'.number_format($row->in_amount, 2, ".", ",").'</td>';
                  break;
                case 5:
                  print '<td></td>';
                  print '<td style="text-align:right !important;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 6:
                  print '<td></td>';
                  print '<td></td>';
                  print '<td></td>';
                  break;
                case 7:
                  print '<td></td>';
                  print '<td style="text-align:right !important;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                /*case 'SET INITIAL VALUE':
                  print '<td></td>';
                  print '<td></td>';
                  print '<td></td>';
                  break;
                case 'CASH ON HAND':
                  print '<td></td>';
                  print '<td>'.$row->out_amount.'</td>';
                  print '<td></td>';
                  break;
                case 'CASH ON CHECK':
                  print '<td></td>';
                  print '<td>'.$row->out_amount.'</td>';
                  print '<td></td>';
                  break;
                case 'FUND TRANSFER':
                  print '<td>'.$row->in_amount.'</td>';
                  print '<td></td>';
                  print '<td></td>';
                  break;
                case 'DEPOSIT':
                  print '<td></td>';
                  print '<td></td>';
                  print '<td>'.$row->in_amount.'</td>';
                  break*/;
              }
              print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->new_fund, 2, ".", ",").'</td>';
              print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->new_hand, 2, ".", ",").'</td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr><td>No transactions.</td><td></td><td></td><td></td><td></td><td></td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MTI -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">MTI</div>
      </div>
      <div class="block-content collapse in">
        <table class="table" style="margin:0px;">
          <thead>
            <tr>
              <th><p>Transaction Date</p></th>
              <th><p>Transfered</p></th>
              <th><p>Withdrawal</p></th>
              <th><p>Deposited</p></th>
              <th><p>Cash In Bank Balance</p></th>
              <th><p>Cash on Hand Balance</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($mti as $row)
            {
              print '<tr>';
              print '<td>'.$row->date.'</td>';
              switch ($row->type)
              {
                case 1:
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->in_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  print '<td></td>';
                  break;
                case 2:
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 3:
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 4:
                  print '<td></td>';
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->in_amount, 2, ".", ",").'</td>';
                  break;
                case 5:
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 6:
                  print '<td></td>';
                  print '<td></td>';
                  print '<td></td>';
                  break;
                case 7:
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
              }
              print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->new_fund, 2, ".", ",").'</td>';
              print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->new_hand, 2, ".", ",").'</td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr><td>No transactions.</td><td></td><td></td><td></td><td></td><td></td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- HPTI -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">HPTI</div>
      </div>
      <div class="block-content collapse in">
        <table class="table" style="margin:0px;">
          <thead>
            <tr>
              <th><p>Transaction Date</p></th>
              <th><p>Transfered</p></th>
              <th><p>Withdrawal</p></th>
              <th><p>Deposited</p></th>
              <th><p>Cash In Bank Balance</p></th>
              <th><p>Cash on Hand Balance</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($hpti as $row)
            {
              print '<tr>';
              print '<td>'.$row->date.'</td>';
              switch ($row->type)
              {
                case 1:
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->in_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  print '<td></td>';
                  break;
                case 2:
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 3:
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 4:
                  print '<td></td>';
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->in_amount, 2, ".", ",").'</td>';
                  break;
                case 5:
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
                case 6:
                  print '<td></td>';
                  print '<td></td>';
                  print '<td></td>';
                  break;
                case 7:
                  print '<td></td>';
                  print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->out_amount, 2, ".", ",").'</td>';
                  print '<td></td>';
                  break;
              }
              print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->new_fund, 2, ".", ",").'</td>';
              print '<td style="text-align:right;padding-right:10xpx;">'.number_format($row->new_hand, 2, ".", ",").'</td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr>
                <td>No transactions.</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>