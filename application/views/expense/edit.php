<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Edit Miscellaneous Expense</div>
      </div>
      <div class="block-content collapse in">

        <form method="post" enctype="multipart/form-data" class="form-horizontal" id="form" style="margin:0px;" onsubmit="return confirm('Please make sure all information are correct before proceeding. Continue?')">
          <?php print form_hidden('mid', $misc->mid); ?>

          <fieldset class="span4">
            <?php
              echo '<div class="control-group">';
              echo form_label('Reference # (SI/OR)', 'or_no', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_input('or_no', set_value('or_no', $misc->or_no));
              echo '</div></div>';

              echo '<div class="control-group date">';
              echo form_label('OR Date', 'or_date', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_input('or_date', set_value('or_date', substr($misc->or_date, 0, 10)), array('class' => 'datepicker'));
              echo '</div></div>';

              echo '<div class="control-group">';
              echo form_label('Amount', 'amount', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_input('amount', set_value('amount', $misc->amount), array('class' => 'numeric'));
              echo '</div></div>';

              echo '<div class="control-group">';
              echo form_label('Type', 'type', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_dropdown('type', $type, set_value('type', $misc->type));
              echo '</div></div>';

              echo '<div class="control-group other hide">';
              echo form_label('Specify others', 'other', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_input('other', set_value('other', $misc->other));
              echo '</div></div>';

              echo '<div class="control-group">';
              echo form_label('CA Reference', 'ca_ref', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_dropdown('ca_ref', $reference, set_value('ca_ref', $misc->ca_ref));
              echo '</div></div>';

              echo '<div class="control-group">';
              echo form_label('Remarks', 'remarks', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo '<textarea name="remarks">'.$misc->remarks.'</textarea>';
              echo '</div></div>';
            ?>
            <div class="form-actions">
              <input type="submit" name="save" value="Save" class="btn btn-success">
            </div>
          </fieldset>

          <div class="span2"></div>

          <div class="upload-form span6">
            <div class="attachments">
              <?php
              foreach ($misc->files as $key => $file)
              {
                print '<div class="attachment temp" style="position:relative">';
                print form_hidden('files[]', $file);

                $path = '/rms_dir/misc/'.$misc->mid.'/'.$file;
                print '<img src="'.$path.'" style="margin:5px; border:solid">';

                print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>';
                print '</div>';
              }

              $temp = set_value('temp', $temp);
              if (!empty($temp))
              {
                foreach ($temp as $file)
                {
                  print '<div class="attachment temp" style="position:relative">';
                  print form_hidden('temp[]', $file);

                  $path = '/rms_dir/temp/'.$file;
                  print '<img src="'.$path.'" style="margin:5px; border:solid">';

                  print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>';
                  print '</div>';
                }
              }
              ?>
            </div>

            <!-- Upload Form -->
            <div class="control-group" style="margin-top: 10px;">
              <div class="control-label">
                Attachment
              </div>
              <div class="controls">
                <input type="file" name="scanFiles" class="input-file uniform_on" id="scanFiles">
                <br><b>Required file format: jpeg, jpg</b>
                <br><b>You can only upload upto 1MB</b>
              </div>
            </div>
            <div class="form-actions">
              <input type="submit" name="upload" value="Upload" class="btn btn-success">
              <!-- <a class="btn btn-success" onclick="upload()">Upload</a> -->
            </div>
          </div>
        </form>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function(){
  $(document).ready(function(){
  	var other = $('.other input').val();
  	var or_date = $('.date input').val();

    $('select[name=type]').change(function(){
      if ($(this).val() == 4) {
        $('.other').removeClass('hide');
      }
      else {
        $('.other').addClass('hide');
        $('.other input').val(other);
      }
    }).change();

    $('input[name=offline]').change(function(){
      if($(this).is(':checked')){
        $('.date').removeClass('hide');
      }
      else {
        $('.date').addClass('hide');
        $('.date input').val(or_date);
      }
    }).change();
  });
});
</script>