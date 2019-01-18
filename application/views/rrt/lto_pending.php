<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Pending at LTO</div>
      </div>
      <div class="block-content collapse in">

<div class="form-horizontal">
  <div class="control-group">
      <div class="control-label">Pending at LTO</div>
      <div class="controls"><?php print '&#x20b1 '.$fund->lto_old; ?></div>
  </div>
  <div class="form-actions">
      <?php print '<input type="button" onclick="nru('.$fund->fid.')" value="NRU" class="btn btn-success">'; ?>
  </div>
</div>

<hr>
<table class="table">
  <thead>
    <th>Date</th>
    <th>NRU</th>
    <th>Pending at LTO</th>
  </thead>
  <tbody>
    <?php 
    foreach ($table as $row)
    {
      print '<tr>';
      print '<td>'.$row->date.'</td>';
      print '<td>'.$row->nru.'</td>';
      print '<td>'.$row->lto_pending.'</td>';
      print '</tr>';
    }

    if(empty($table)) {
      print '<tr>
        <td>No history record found.</td>
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
<div class="modal fade" id="modal_form_nru" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">NRU</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error hide">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <form action="#" id="form_nru" class="form-horizontal">
        <div class="form-body">
          <div class="form-group" style="margin-bottom:15px;">  
            <label class="control-label col-md-3" style="margin-right:10px;">Cash on Hand</label>
            <div class="col-md-9" id="cash">
              
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label col-md-3" style="margin-right:10px;">NRU</label>
            <div class="col-md-9">
              <input name="nru" class="form-control" type="text" value="0.00">
            </div>
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSet" onclick="set_nru()" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->