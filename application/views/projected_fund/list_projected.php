<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Projected Funds</div>
      </div>
      <div class="row">
        <div class="offset4 span4 offset4">

          <?php echo form_open("projected_fund/ca_template", ["target"=>"_blank", "class"=>"form-inline"]); ?>
            <fieldset>
              <legend style="font-size:14px; margin-bottom:0;">Download CA Template</legend>
              <div class="control-group" style="margin-top:10px;">
                <?php echo form_label("CA Date", "ca_date", ["class"=>"control-label"]); ?>
                <div class="controls">
                  <?php echo form_input(["id"=>"ca-date", "class"=>"datepicker text-center", "name"=>"date", "value"=>date('Y-m-d') ]); ?>
                  <?php echo form_button([ "class"=> "btn btn-warning", "type"=>"submit", "content" => "Download" ]); ?>
                </div>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
      <div class="block-content collapse in">
        <?php echo $table; ?>
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
      <div id="alert-status" class="alert">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <div class="modal-body form">
        <div class="form-body">
          <!-- see create_voucher.php -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
