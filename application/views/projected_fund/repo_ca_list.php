<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo CA List</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <fieldset>
            <div class="control-group span5">
              <div class="control-label">Entry Date</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-1 day'))), array('class' => 'datepicker')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker')); ?>
              </div>
            </div>

            <?php
              //$status = array('_any' => '- Any -') + $status;
              echo '<div class="control-group span5">';
              echo form_label('Status', 'status', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_dropdown('status', [ '0' => '- Any -', 'FOR CA' => 'For Ca', 'FOR DEPOSIT' => 'For Deposit', 'DEPOSITED' => 'Deposited'], set_value('status', '0'));
              echo '</div></div>';

              echo '<div class="control-group span5">';
              echo form_label('Region', 'region_id', array('class' => 'control-label'));
              echo '<div class="controls">';
              switch ($_SESSION['position']) {
                case 108: // RRT-SPVSR
                  $set_region = $_SESSION['region_id'];
                  $bool = array('readonly' => true);
                  break;

                default: // TRSRY-SPVSR, ACCT-PAYCL, RRT-MGR
                  $set_region = set_value('region_id');
                  $bool = false;
                  break;
              }
              echo form_dropdown('region_id', $region_dropdown, $set_region, $bool);
              echo '</div></div>';
            ?>
            <div class="form-actions span4">
              <input type="submit" class="btn btn-success" value="Apply">
            </div>
          </fieldset>
        </form>
        <hr>
        <?php echo $table; ?>
      </div>
    </div>
  </div>
</div>
<?php echo form_open("repo/print_ca", ["id"=>"print", "class"=>"hide", "target"=>"_blank"]) ?>
<script>
  $('button[name="save"]').on('click', function() {
    console.log($(this).val());;
  });

  $('button[name="print"]').on('click', function() {
    var repo_batch_id = $(this).val();
    var form_print = $("#print");
    form_print.empty().append("<input name='repo_batch_id' value='"+repo_batch_id+"'>");
    form_print.submit();
  });
</script>
