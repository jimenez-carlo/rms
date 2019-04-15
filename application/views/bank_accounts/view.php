<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Fund</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <tr>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th><p>Maintaining Balance</p></th>
              <th><p>Bank</p></th>
              <th><p>Account Number</p></th>
              <th><p>Signatory 1</p></th>
              <th><p>Signatory 2</p></th>
              <th><p>Signatory 3</p></th>
              <th width="150"><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {

              $key = '['.$row->fid.']';
              print '<tr>';
              print '<td>'.$row->region.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->m_balance,2,'.',',').'</td>';
              print '<td>'.$row->bank.'</td>';
              print '<td>'.$row->acct_number.'</td>';
              print '<td>'.$row->sign_1.'</td>';
              print '<td>'.$row->sign_2.'</td>';
              print '<td>'.$row->sign_3.'</td>';

              print '<td><button class="btn btn-success" onclick="manage('.$row->fid.')">Manage</button></td>';

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
      <h3 class="modal-title">Manage Bank Account</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error hide">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <form action="#" id="form" class="form-horizontal" style="margin:0px!important;">
        <div class="form-body">
          <!-- rrt fund -->
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save()" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
