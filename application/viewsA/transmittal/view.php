<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
    <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Transmittal # <?php print $transmittal->trans_no; ?></div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <th><p>Customer Name</p></th>
            <th><p>CR #</p></th>
            <th><p>Status</p></th>
            <th><p>Last remarks</p></th>
            <th><p></p></th>
          </thead>
          <tbody>
            <?php
            foreach ($transmittal->sales as $sales)
            {
              print '<tr>';
              print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
              print '<td>'.$sales->cr_no.'</td>';
              print '<td>'.$sales->status.'</td>';

              print (!empty($sales->last_user))
                ? '<td>by '.$sales->last_user->firstname.' '.$sales->last_user->lastname.'</td>'
                : '<td><i>No remarks.</i></td>';

              print '
              <td>
                <a class="btn btn-success" onclick="view('.$transmittal->tid.','.$sales->sid.')">';
                if($_SESSION['position']==108) print 'Unhold'; else print 'View Remarks';
              print '</a> ';
              if($_SESSION['position']!=108) print '<a class="btn btn-success" href="../receive/'.$sales->sid.'">Tag as Received</a>';
              print '
              </td>';
              print '</tr>';
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
        <h3 class="modal-title">Fund</h3>
      </div>
      <div class="modal-body form">
        <div class="alert alert-error hide">
          <button class="close" data-dismiss="alert">&times;</button>
          <div class="error"></div>
        </div>
        <form action="#" id="form" class="form-horizontal">
          <div class="form-body">
            <div class="form-group remarks"></div>

            <div class="form-group">
              <label class="control-label" style="margin-right:10px;">New Remarks</label>
              <div class="controls" style="margin-right:10px;">
                <textarea name="remarks" placeholder="New remarks"></textarea>
              </div>
            </div>
          </div>
        </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnSubmit" onclick="save()" class="btn btn-success">Save remarks</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
