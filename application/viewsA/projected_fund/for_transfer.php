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
        <table class="table">
          <thead>
            <tr>
              <th><p>Debit Memo #</p></th>
              <th><p>Voucher Date</p></th>
              <th style="text-align:right;padding-right:10px;"><p>Amount</p></th>
              <th><p>Voucher #</p></th>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->dm_no.'</td>';
              print '<td>'.$row->date.'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->amount,2,'.',',').'</td>';
              print '<td>'.$row->voucher_no.'</td>';
              print '<td>'.$row->region.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td style="text-align:center"><button class="btn btn-success" onclick="transfer_fund('.$row->fid.')">Transfer Fund</button></td>';
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
        <h3 class="modal-title">Transfer Fund</h3>
      </div>
      <div class="modal-body form">
        <div class="alert alert-error hide">
          <button class="close" data-dismiss="alert">&times;</button>
          <div class="error"></div>
        </div>
        <div class="form-body">
          <!-- see transfer_fund.php -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save_transfer()" class="btn btn-success">Save Transfer</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->