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
              <th style="text-align:right;padding-right:10px;"><p>Cash on Check</p></th>
              <th style="text-align:center" colspan="2"><p>Total Projected Cost</p></th>
              <th><p></p></th>
            </tr>
            <tr>
              <th colspan="5"></th>
              <th style="text-align:right;padding-right:10px;"><p>For Voucher</p></th>
              <th style="text-align:right;padding-right:10px;"><p>For Transfer</p></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->region.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->fund,2,'.',',').'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->cash_on_hand,2,'.',',').'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->cash_on_check,2,'.',',').'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->voucher,2,'.',',').'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->transfer,2,'.',',').'</td>';

              if ($row->voucher > 0 && $position == 3)
                print '<td style="text-align:center"><button class="btn btn-success" onclick="create_voucher('.$row->fid.')">Create Voucher</button></td>';
              else print '<td></td>';

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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Projected Funds</h3>
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
        <button type="button" id="btnSave" onclick="save_voucher()" class="btn btn-success">Save Voucher</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->