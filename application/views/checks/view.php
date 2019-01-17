<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid form-horizontal">
	<div class="row-fluid">

    <!-- Attachment Block -->
    <div class="block span8">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Attachment</div>
      </div>
      <div class="block-content collapse in">
        <?php
        if (!empty($sales->files))
        {
          foreach ($sales->files as $key => $file)
          {
            print '<div class="attachment" style="position:relative">';
            print form_hidden('filekeys[]', $key);

					  $exp = explode('.', $file);
					  $ext = array_pop($exp);
					  $path = './../../rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine->engine_no.'/'.$file;

            print '<img src="'.$path.'" style="margin:1em; border:solid">';

            print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 1em">X</a>';
            print '</div>';
          }
        }
        else
        {
        	print 'No attachments.';
        }
        ?>
			</div>
		</div>

		<!-- Sales Block -->
    <div class="block span4">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Sales</div>
      </div>
      <div class="block-content collapse in">
				<div class="control-group">
				  <div class="control-label">Engine #</div>
				  <div class="controls"><?php print $sales->engine_no; ?></div>
				</div>
				<div class="control-group">
				  <div class="control-label">Branch</div>
				  <div class="controls"><?php print $sales->branch->b_code.' '.$sales->branch->name; ?></div>
				</div>
				<div class="control-group">
				  <div class="control-label">Customer Name</div>
				  <div class="controls"><?php print $sales->first_name.' '.$sales->last_name; ?></div>
				</div>
				<div class="control-group">
				  <div class="control-label">Type of Sales</div>
				  <div class="controls"><?php print $sales->sales_type; ?></div>
				</div>

        <div class="control-group">
          <?php
            echo form_label('Registration', 'registration', array('class' => 'control-label'));
            echo '<div class="controls text">'.$sales->registration.'</div>';
          ?>
        </div>
        <div class="control-group">
          <?php
            echo form_label('Tip', 'tip', array('class' => 'control-label'));
            echo '<div class="controls text">'.$sales->tip.'</div>';
          ?>
        </div>
        <div class="control-group">
          <?php
            echo form_label('CR #', 'cr_no', array('class' => 'control-label'));
            echo '<div class="controls text">'.$sales->cr_no.'</div>';
          ?>
        </div>
        <div class="control-group">
          <?php
            echo form_label('MVF #', 'mvf_no', array('class' => 'control-label'));
            echo '<div class="controls text">'.$sales->mvf_no.'</div>';
          ?>
        </div>
        <div class="control-group">
          <?php
            echo form_label('Plate #', 'plate_no', array('class' => 'control-label'));
            echo '<div class="controls text">'.$sales->plate_no.'</div>';
          ?>
        </div>
			</div>
		</div>
	</div>
</div>