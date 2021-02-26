<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
              <div class="pull-left">Customer Status</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <?php print form_hidden('sid', 0); ?>
          <fieldset>
            <?php
              if (isset($branches))
              {
                $branches = array('0' => '- Any -') + $branches;
                echo '<div class="control-group span5">';
                echo form_label('Branch', 'branch', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_dropdown('branch', $branches, set_value('branch', $branch_def));
                echo '</div></div>';

                $status = array_merge(array('_any' => '- Any -'), $status);
                echo '<div class="control-group span5">';
                echo form_label('Status', 'status', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_dropdown('status', $status, set_value('status'));
                echo '</div></div>';

                echo '<div class="control-group span5">';
                echo form_label('Customer Name', 'name', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_input('name', set_value('name'));
                echo '</div></div>';

                echo '<div class="control-group span5">';
                echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_input('engine_no', set_value('engine_no'));
                echo '</div></div>';
              }
              else
              {
                echo '<div class="control-group span5">';
                echo form_label('Customer Name', 'name', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_input('name', set_value('name'));
                echo '</div></div>';

                $status['_any'] = '- Any -';
                asort($status);
                echo '<div class="control-group span5">';
                echo form_label('Status', 'status', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_dropdown('status', $status, set_value('status'));
                echo '</div></div>';

                echo '<div class="control-group span5">';
                echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_input('engine_no', set_value('engine_no'));
                echo '</div></div>';
              }

                echo '<div class="control-group span5">';
                echo form_label('Plate #', 'plate_number', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_input('plate_number', set_value('plate_number'));
                echo '</div></div>';

                echo '
                <div class="form-actions span5">
                  <input type="submit" class="btn btn-success" value="Search" name="submit">
                </div>';

            ?>
          </fieldset>
          <hr>
          <?php echo $table; ?>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function(){
        $(document).ready(function(){
                $(".table").dataTable({
                        "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
                        "sPaginationType": "bootstrap",
                        "oLanguage": {
                                "sLengthMenu": "_MENU_ records per page"
                        },
                        "bFilter": false,
                        "bSort": false,
                        "iDisplayLength": 5,
                        "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
                });

                $('input[name=submit]').click(function(){
                        $('form').removeAttr('action').removeAttr('target');
                });
                $('table').on('click', '.view', function(){
                        $('form').attr('action', 'sales/view').attr('target', '_blank');
                });
                $('table').on('click', 'input[name=print_orcr]', function(){
                        var sid = $(this).attr('data-value');
                        $('input[name=sid]').val(sid);
                        $('form').attr('action', 'sales/print_orcr').attr('target', '_blank');
                });
        });
});
</script>
