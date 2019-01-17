<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">
.form-horizontal .control-group {
	margin-bottom: 3px!important;
  font-size: 9pt!important;
}
.form-horizontal .control-label {
	width: 115PX!important;
  font-size: 9pt!important;
}
.form-horizontal .controls {
  margin-left: 135px!important;
  margin-right: 20px!important;
}
.form-horizontal input[type='text'] {
    width: 100%;
    height: 18px;
    font-size: 9pt;
}
</style>

<form method="post" enctype="multipart/form-data" class="form-horizontal" id="form" style="margin:0px;">

<div class="container-fluid">
	<div class="row-fluid">

    <!-- attachment block -->
    <div class="block attachment-block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Attachment</div>
      </div>
      <div class="block-content collapse in">
        <div class="<?php if (isset($spvsr)) print 'upload-form'; ?>">
          <div class="attachments">
            <?php
            if (!empty($files))
            {
              foreach ($files as $key => $file)
              {
                print '<div class="attachment temp" style="position:relative">';
                print form_hidden('files[]', $file);

                $path = './rms_dir/temp/'.$file;
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
              Upload File
            </div>
            <div class="controls">
              <input type="file" name="scanFiles[]" class="input-file uniform_on" id="scanFiles" multiple>
              <br><b>Required file format: jpeg, jpg</b>
              <br><b>You can only upload upto 1MB</b>
            </div>
          </div>
          <div class="form-actions">
          	<a class="btn btn-success" onclick="upload()">Upload</a>
          </div>
        </div>
			</div>
		</div>

    <!-- sales block -->
    <div class="block span3 sales-block hide">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Expense</div>
      </div>
      <div class="block-content collapse in">
        <?php if (empty($sales)) { ?>
        <div class="form-group">
          <?php
            echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
            echo '<div class="controls">';
            echo form_input('engine_no', set_value('engine_no'), array('style' => 'width:100%'));
            echo '</div>';
          ?>
        </div>
        <div class="form-actions">
          <input type="submit" name="search" value="Search" class="btn btn-success">
        </div>
        <?php } else print $sales; ?>
			</div>
		</div>
	</div>
</div>

</form>
