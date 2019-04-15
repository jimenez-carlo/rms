<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Reimbursement</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <tr>
              <th><p>Batch #</p></th>
              <th><p>Region</p></th>
              <th><p>Date</p></th>
              <th><p>Total Amount</p></th>
              <th><p>Balance</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->batch_no.'</td>';
              print '<td>'.$row->region.'</td>';
              print '<td>'.$row->date.'</td>';
              print '<td>'.$row->amount.'</td>';
              print '<td>'.$row->balance.'</td>';
              print '<td><button class="btn btn-success" onclick="reimbursement('.$row->bid.')">Reimbursement</button></td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr>
                <td>No result.</td>
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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">Reimbursement</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error hide">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <form action="#" id="form" class="form-horizontal">
        <div class="form-body">
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">Batch #</label>
            <div class="col-md-9">
              <span class="batch-no"></span>
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">Document #</label>
            <div class="col-md-9">
              <input name="doc_no" class="form-control" type="text">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">Spool #</label>
            <div class="col-md-9">
              <input name="spool_no" class="form-control" type="text">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">Voucher #</label>
            <div class="col-md-9">
              <input name="voucher_no" class="form-control" type="text">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">Check #</label>
            <div class="col-md-9">
              <input name="check_no" class="form-control" type="text">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">Amount</label>
            <div class="col-md-9">
              <span class="batch-balance"></span>
            </div>
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save()" class="btn btn-success">Set</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->