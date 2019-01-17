<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.form-horizontal .controls {
    padding-top: 5px;
}
</style>

<div class="container-fluid form-horizontal">
	<div class="row-fluid">
    <!-- block -->
    <div class="block span2">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Transaction #</div>
      </div>
      <div class="block-content collapse in">

        <!-- List Form -->
        <form class="form-horizontal" method="post" style="margin:0;">
          <table class="table" style="margin:0;">
            <tbody>
              <?php
              foreach ($table as $row)
              {
                if ($row->alert > 0) print '<tr class="warning">';
                else print '<tr>';
                
                print '<td><a class="btn ';
                if($row->status == "Done") print 'btn-success'; else print 'btn-warning';
                print ' btn-mini" href="'.$dir.'orcr_checking/view/'.$row->tid.'"><i class="icon-edit"></i></a> '.$row->trans_no.'</td>';
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
        </form>

			</div>
		</div>


    <!-- block -->
    <div class="block span10">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Topsheet <?php if(isset($tid)) print '('.$topsheet->trans_no.')'; ?></div>
      </div>
      <div class="block-content collapse in">
        <p><span class="icon icon-chevron-left"></span> Select a topsheet to check OR CR attachment and details.</p>
  </div>
</div>



<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">Hold</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error hide">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <form action="#" id="form" class="form-horizontal">
        <div class="form-body">
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label" style="margin-right:10px;">Reason</label>
            <div class="controls reason">
                <select multiple name="reason[]">
                   <option value="1">Wrong Attachment</option>
                   <option value="2">Wrong Registration Amount</option>
                   <option value="3">Wrong Tip Amount</option>
                   <option value="4">Wrong CR #</option>
                   <option value="5">Wrong MVF #</option>
                   <option value="6">Wrong Plate #</option>
                   <option value="0" class="others">Others</option>
                </select>
            </div>
            <div class="controls reason0 hide">
                <select multiple name="reason[]">
                   <option value="1">Wrong Attachment</option>
                   <option value="2">Wrong Meal Amount</option>
                   <option value="3">Wrong Photocopy Amount</option>
                   <option value="4">Wrong Transportation Amount</option>
                   <option value="5">Wrong Others Amount</option>
                   <option value="0" class="others">Others</option>
                </select>
            </div>
          </div>

          <div class="form-group remarks hide" style="margin-bottom:15px;">
            <label class="control-label" style="margin-right:10px;">Remarks</label>
            <div class="controls">
                <textarea name="remarks"></textarea>
            </div>
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnWithdraw" onclick="save_hold()" class="btn btn-success">Hold</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->