<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Run Cron</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post" onsubmit="return confirm('Manual cron run cannot be reverted without admin help. Continue?');">
          <input type="hidden" name="force" value="0">

          <fieldset>
            <div class="control-group">
              <div class="control-label">Date From</div>
              <div class="controls">
                <?php
                print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-7 days'))), array('class' => 'datepicker')) ?>
              </div>
            </div>
            <div class="control-group">
              <div class="control-label">Date To</div>
              <div class="controls">
                <?php
                print form_input('date_yesterday', set_value('date_yesterday', date('Y-m-d', strtotime('-1 days'))), array('class' => 'datepicker')) ?>
              </div>
            </div>
          </fieldset>

          <hr>
          <div class="form-actions">
            <input type="submit" name="submit[1]" value="Create RMS data based on data from LTO Transmittal System [rms_create]" class="btn btn-success">
            <hr>
            <input type="submit" name="submit[2]" value="Update rms_expense table for BOBJ Report [rms_expense]" class="btn btn-success">
            <hr>
            <input type="submit" name="submit[3]" value="Update Sales AR Amount from BOBJ [ar_amount]" class="btn btn-success">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    if ($('#trigger-force').length > 0) {
      $('#trigger-force').html('Are you sure you want to rerun this Cron?');
      $('#trigger-force').append(' <a id="trigger-force-yes">Yes.</a>');
      $('#trigger-force').append(' <a id="trigger-force-no">Nevermind.</a>');
      $('#trigger-force').removeClass('hide');
    }

    $('#trigger-force-no').click(function(){
      $('#trigger-force').html('');
    });

    $('#trigger-force-yes').click(function(){
      $('#trigger-force').html('');
      var key = $('#trigger-force').attr('data-key');
      $('input[name=force]').val(key);
      $('form').submit();
    });
  });
</script>
