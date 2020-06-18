<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Projected Funds</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <tr>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th style="text-align:right;padding-right:10px;"><p>Cash in Bank</p></th>
              <th style="text-align:right;padding-right:10px;"><p>Cash on Hand</p></th>
              <th style="text-align:center" colspan="2"><p>Total Projected Cost</p></th>
              <?php if ($position == 3) print '<th><p></p></th>'; ?>
            </tr>
            <tr>
              <th colspan="4"></th>
              <th style="text-align:right;padding-right:10px;"><p>For CA</p></th>
              <th style="text-align:right;padding-right:10px;"><p>For Deposit</p></th>
              <?php if ($position == 3) print '<th><p></p></th>'; ?>
            </tr>
          </thead>
          <tbody>
            <?php
            $comps = ($_SESSION['company'] != 8) ? array(1 => 'MNC', 3 => 'HPTI', 6 => 'MTI') : array(8 => 'MDI');
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->region.'</td>';
              foreach ($comps as $key => $comp) {
                switch ($key) {
                  case 1: $voucher = $row->voucher_1; $transfer = $row->transfer_1; break;
                  case 3: $voucher = $row->voucher_3; $transfer = $row->transfer_3; break;
                  case 6: $voucher = $row->voucher_6; $transfer = $row->transfer_6; break;
                  case 8: $voucher = $row->voucher_8; $transfer = $row->transfer_8; break;
                }

                if ($key !=8 && $key > 1) {
                  print '<td></td><td>'.$comp.'</td>';
                  print '<td style="text-align:right;padding-right:10px;">-</td>';
                  print '<td style="text-align:right;padding-right:10px;">-</td>';
                } else {
                  print '<td>'.$comp.'</td>';
                  print '<td style="text-align:right;padding-right:10px;">'.number_format($row->fund,2,'.',',').'</td>';
                  print '<td style="text-align:right;padding-right:10px;">'.number_format($row->cash_on_hand,2,'.',',').'</td>';
                }

                print '<td style="text-align:right;padding-right:10px;">'.number_format($voucher,2,'.',',').'</td>';
                print '<td style="text-align:right;padding-right:10px;">'.number_format($transfer,2,'.',',').'</td>';

                if ($position == 3) {
                  $disabled = ($voucher > 0) ? '' : 'disabled';
                  print '<td style="text-align:center"><button class="btn btn-success '.$disabled.'" onclick="create_voucher('.$row->fid.', '.$key.')" '.$disabled.'>Create CA</button></td>';
                }
                print '</tr><tr>';
              }

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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog" style="width: 85%; left: 30%;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Create CA</h3>
      </div>
      <div class="modal-body form">
        <div class="alert alert-error hide">
          <button class="close" data-dismiss="alert">&times;</button>
          <div class="error"></div>
        </div>
        <div class="form-body">
          <!-- see create_voucher.php -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save_voucher()" class="btn btn-success">Save CA</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
