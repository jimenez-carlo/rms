<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">OR CR</div>
            </div>
            <div class="block-content collapse in">

                <form class="form-horizontal" method="post" style="margin:10px 0px">
                  <fieldset>

                    <!-- Search Form -->
                    <div class="control-group" style="margin:0">
                      <?php
                        echo form_label('Engine #', 'engine_no', array('class' => 'control-label'));
                        echo '<div class="controls">';
                        echo form_input('engine_no', set_value('engine_no'));
                        echo '<input type="submit" class="btn btn-success" value="Search" name="search">';
                        echo '</div>';
                      ?>
                    </div>


                  </fieldset>
                </form>

			</div>
		</div>
    </div>
</div>

<?php if(isset($sales)) { ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">OR CR Attachment</div>
            </div>
            <div class="block-content collapse in">
                <div class="form-actions span12">
                  <a class="btn btn-success" href="print_orcr/<?php print $sales->sid; ?>" style="float:right; margin-right:5em">Print</a>
                </div>

                <?php
                foreach ($sales->files as $file)
                {
                    $path = './../rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/'.$file;

                    print '<img src="'.$path.'" style="margin:1em; border:solid; float:left; width: 47%">';
                }
                if (empty($sales->files))
                {
                    print "No attachments.";
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>