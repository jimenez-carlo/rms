<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Liquidation</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <fieldset>
            <div class="control-group span5">
              <div class="control-label">Date Deposited</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-3 days'))), array('class' => 'datepicker', 'autocomplete' => 'off')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker', 'autocomplete' => 'off')); ?>
              </div>
            </div>

            <?php
            if ($_SESSION['position'] != 108)
            {
              $options = array('_none' => '- Any -') + $region;
              print '<div class="control-group span4">';
              print '<div class="control-label">Region</div>';
              print '<div class="controls">';
              print form_dropdown('region', $options, set_value('region'));
              print '</div></div>';
            }
            ?>

            <div class="form-actions span12">
              <input class="btn btn-success" type="submit" name="search" value="Search">
              <input class="btn btn-warning" type="submit" name="download" value="Download">
            </div>
          </fieldset>
        </form>

        <hr>
        <?php echo (!empty($table)) ? $table : 'Not Found!'; ?>
      </div>
    </div>
  </div>
</div>

<form id="form_liq" method="post" action="<?= base_url() ?>liquidation/sales" target="_blank">
  <input type="hidden" name="vid" value="0" class="vid">
</form>

<script type="text/javascript">
  $(function(){
    $(document).ready(function(){
      $('#table_liq').on('click', 'tbody tr .vid', function(){
        var vid = $(this).attr('data-vid');
        if (vid) {
          $('#form_liq input.vid').val(vid);
          $('#form_liq').submit();
        }
      });
    });
  });
</script>
