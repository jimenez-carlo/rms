<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">
.form-horizontal .control-group {
	margin-bottom: 7px!important;
}
.form-horizontal .control-label {
	width: 115PX!important;
}
.form-horizontal .controls {
	margin-left: 135px!important;
	margin-right: 20px!important;
}
.form-horizontal .sales-form input[type="text"] {
	width:130px!important;
}
</style>

<div class="container-fluid">
	<div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">
        <?php
        if (isset($spvsr)) print 'Validation';
        else 'Attachment';
        ?>
        </div>
      </div>
      <div class="block-content collapse in">
				<form method="post" enctype="multipart/form-data" class="form-horizontal" id="form" style="margin:10px 0px;">

	        <div class="span9 <?php if (isset($spvsr)) print 'upload-form'; ?>">
	          <!-- Attachments -->
	          <?php
	          // SCAN DOC FILES
	          if (!empty($sales->files))
	          {
	            foreach ($sales->files as $key => $file)
	            {
	              print '<div class="attachment" style="position:relative">';
	              print form_hidden('filekeys[]', $key);

							  $path = './rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine->engine_no.'/'.$file;
	              print '<img src="'.$path.'" style="margin:5px; border:solid">';

	              print '<a style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>';
	              print '</div>';
	            }
	          }

	          if (!empty($tempkeys))
	          {
	            foreach ($tempkeys as $key => $file)
	            {
	              print '<div class="attachment temp" style="position:relative">';
	              print form_hidden('tempkeys[]', $file);

	              $path = './rms_dir/temp/'.$file;
	              print '<img src="'.$path.'" style="margin:5px; border:solid">';

	              print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>';
	              print '</div>';
	            }
	          }

	          // TEMP FILES
	          /*if (!empty($_SESSION['files']))
	          {
	            foreach ($_SESSION['files'] as $key => $file)
	            {
	              print '<div class="attachment temp" style="position:relative">';
	              print form_hidden('tempkeys[]', $key);

	              $path = './rms_dir/temp/'.$file['file_name'];
	              print '<img src="'.$path.'" style="margin:5px; border:solid">';

	              print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 5px">X</a>';
	              print '</div>';
	            }
	          } */
	          ?>
	          <div class="attachments">
	          </div>
	          <!-- Upload Form -->
	          <div class="control-group" style="margin-top: 10px;">
	            <div class="control-label">
	              Upload File
	            </div>
	            <div class="controls">
	              <input type="file" name="scanFiles[]" class="input-file uniform_on" id="scanFiles" multiple>
                <br>
                <b>Required file format: jpeg, jpg</b>
                <br><b>You can only upload upto 1MB</b>
	            </div>
	          </div>
	          <div class="form-actions">
	          	<a class="btn btn-success" onclick="upload_img()">Upload</a>
	          </div>
	        </div>

	        <?php if (!empty($sales)) { ?>
	        <div class="span3 sales-form">
	          <!-- Sales Form -->
	          <?php
	          print form_hidden('sid', set_value('sid', $sales->sid));
	          print form_hidden('pos_expense', set_value('pos_expense', '0'));
	          print form_hidden('neg_expense', set_value('neg_expense', '0'));
	          ?>
	          <span class="cash hide"><?php echo $cash_on_hand; ?></span>
	          <span class="registration hide"><?php echo $sales->registration; ?></span>
	          <span class="tip hide"><?php echo $sales->tip; ?></span>

	          <div class="control-group">
	            <div class="control-label">
	              Cash on Hand
	            </div>
	            <div class="controls cash-on-hand">
	              <?php print $cash_on_hand; ?>
	            </div>
	          </div>
	          <div class="control-group">
	            <div class="control-label">
	              Engine #
	            </div>
	            <div class="controls">
	              <?php print $sales->engine->engine_no; ?>
	            </div>
	          </div>
	          <div class="control-group">
	            <div class="control-label">
	              Branch
	            </div>
	            <div class="controls">
	              <?php print $sales->branch->b_code.' '.$sales->branch->name; ?>
	            </div>
	          </div>
	          <div class="control-group">
	            <div class="control-label">
	              Customer Name
	            </div>
	            <div class="controls">
	              <?php print $sales->customer->first_name
	                      .' '.$sales->customer->middle_name
	                      .' '.$sales->customer->last_name; ?>
	            </div>
	          </div>
	          <div class="control-group">
	            <div class="control-label">
	              Type of Sales
	            </div>
	            <div class="controls">
	              <?php print $sales->sales_type; ?>
	            </div>
	          </div>

	          <div class="control-group">
	            <?php
	              echo form_label('Registration', 'registration', array('class' => 'control-label'));
	              echo '<div class="controls">';
	              echo form_input('registration', set_value('registration', $sales->registration), array('class' => 'numeric'));
	              echo '</div>';
	            ?>
	          </div>
	          <div class="control-group">
	            <?php
	              echo form_label('Tip', 'tip', array('class' => 'control-label'));
	              echo '<div class="controls">';
	              echo form_input('tip', set_value('tip', $sales->tip), array('class' => 'numeric'));
	              echo '</div>';
	            ?>
	          </div>
	          <div class="control-group">
	            <?php
	              echo form_label('CR #', 'cr_no', array('class' => 'control-label'));
	              echo '<div class="controls">';
	              echo form_input('cr_no', set_value('cr_no', $sales->cr_no));
	              echo '</div>';
	            ?>
	          </div>
	          <div class="control-group">
	            <?php
	              echo form_label('MVF #', 'mvf_no', array('class' => 'control-label'));
	              echo '<div class="controls">';
	              echo form_input('mvf_no', set_value('mvf_no', $sales->mvf_no));
	              echo '</div>';
	            ?>
	          </div>
	          <div class="control-group">
	            <?php
	              echo form_label('Plate #', 'plate_no', array('class' => 'control-label'));
	              echo '<div class="controls">';
	              echo form_input('plate_no', set_value('plate_no', $sales->plate_no));
	              echo '</div>';
	            ?>
	          </div>
	          <div style="text-align:center;">
	            <input type="submit" class="btn btn-success" value="Save" name="submit">
	            <a class="btn btn-success calculate hide">Calculate</a>
	            <?php
                if (isset($spvsr))
                {
              	  print '<a href="registration" class="btn btn-success">Cancel</a>';
                }
                ?>
	          </div>
	        </div>

          <?php } else { ?>
          <!-- Search Form -->
          <div class="<?php if (!isset($spvsr)) print 'search-form span3'; ?> " style="margin-left:0px;">
            <div class="control-group <?php if(isset($spvsr)) echo 'span4'; ?>">
              <?php
                echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
                echo '<div class="controls">';
                echo form_input('engine_no', set_value('engine_no'), array('style' => 'width:100%'));
                if(!isset($spvsr)) echo '<br><br><input type="submit" class="btn btn-success" value="Search" name="search">';
                echo '</div>';
              ?>
            </div>
            <?php
            if(isset($spvsr))
            {
            	echo '<div class="span4">';
            	echo '<input type="submit" class="btn btn-success" value="Search" name="search">';
            	echo '</div>';
            }
            ?>
          </div>
          <?php } ?>
				</form>

			</div>
		</div>
	</div>
</div>
