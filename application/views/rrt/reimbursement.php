<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Pending at Accounting</div>
      </div>
      <div class="block-content collapse in">

<div class="form-horizontal">
  <div class="control-group">
      <div class="control-label">Pending at Accounting</div>
      <div class="controls"><?php print '&#x20b1 '.$fund->acct_old; ?></div>
  </div>
  <div class="form-actions">
      <?php print '<input type="button" onclick="topsheet('.$fund->fid.')" value="Batch" class="btn btn-success">'; ?>
  </div>
</div>

<hr>
<table class="table">
  <thead>
    <th>Date</th>
    <th>Batch #</th>
    <th>Batch Amount</th>
    <th>Pending at Accounting</th>
  </thead>
  <tbody>
    <?php 
    foreach ($table as $row)
    {
      print '<tr>';
      print '<td>'.$row->date.'</td>';
      print '<td>'.$row->batch_no.'</td>';
      print '<td>'.$row->amount.'</td>';
      print '<td>'.$row->acct_pending.'</td>';
      print '</tr>';
    }

    if(empty($table)) {
      print '<tr>
        <td>No history record found.</td>
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
<div class="modal fade" id="modal_form_topsheet" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">Batch</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error hide">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <form action="#" id="form_topsheet" class="form-horizontal">
        <div class="form-body">
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">Batch #</label>
            <div class="col-md-9" id="cash">
              <input name="batch_no" class="form-control" type="text">
              <span class="batch-no hide">0.00</span>
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">Batch Amount</label>
            <div class="col-md-9">
              <input name="batch_amount" class="form-control" type="hidden">
              <span class="batch-amount">0.00</span>
            </div>
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnBatch" onclick="get_batch()" class="btn btn-success">Load Batch</button>
        <button type="button" id="btnTopsheet" onclick="set_topsheet()" class="btn btn-success hide">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->