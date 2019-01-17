<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Attachment</div>
      </div>
      <div class="block-content collapse in">

      	<form method="post" enctype="multipart/form-data" class="form-horizontal">
	        <div class="span7 <?php if (isset($spvsr)) print 'upload-form'; ?>">
	          <!-- Attachments -->
	          <?php
	          if (!empty($sales->files))
	          {
	            foreach ($sales->files as $key => $file)
	            {
	              print '<div class="attachment" style="position:relative">';
	              print form_hidden('filekeys[]', $key);

							  $exp = explode('.', $file);
							  $ext = array_pop($exp);
							  $path = '/rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine->engine_no.'/'.$file;

	              print '<img src="'.$path.'" style="margin:1em; border:solid">';

	              print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 1em">X</a>';
	              print '</div>';
	            }
	          }

	          if (!empty($_SESSION['files']))
	          {
	            foreach ($_SESSION['files'] as $key => $file)
	            {
	              print '<div class="attachment temp" style="position:relative">';
	              print form_hidden('tempkeys[]', $key);

	              $path = '/rms_dir/temp/'.$file['file_name'];
	              print '<img src="'.$path.'" style="margin:1em; border:solid">';

	              print '<a href="#" style="background:#BDBDBD; color:black; padding:0.5em; position:absolute; top: 1em">X</a>';
	              print '</div>';
	            }
	          }
	          ?>

	          <!-- Upload Form -->
	          <div class="control-group">
	            <div class="control-label">
	              Upload File
	            </div>
	            <div class="controls">
	              <input type="file" name="scanFiles[]" class="input-file uniform_on" multiple>
	              <br>
	              <b>Required file format: jpeg, jpg</b>
	              <br><b>You can only upload upto 1MB</b>
	            </div>
	          </div>
	          <div class="form-actions">
	            <input type="submit" name="upload" class="btn btn-success" value="Upload">
	          </div>

	          <?php
	          // Remarks
	          print '<hr><div><b>REMARKS</b></div><hr>';
	          if (!empty($sales->remarks))
	          {
	            foreach ($sales->remarks as $row)
	            {
	              print '
	              <div>
	                <p>'.$row->remarks.'</p>
	                <p><i>by '.$row->remarks_name.' ('.$row->remarks_user.') on '.$row->remarks_date.'</i></p>
	              </div>';
	            }
	          }
	          else
	          {
	            print '<div>No remarks.</div>';
	          }
	          ?>
	        </div>

	        <!-- Sales Form -->
	        <div class="span5 sales-form">
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

	          <div class="control-group">
	            <div class="control-label">Reason</div>
	            <div class="controls">
	              <?php
	              foreach ($sales->reason as $reason)
	              {
	              	print '<div style="float: left; font-style: italic; border: 1px solid red; padding: 2px 5px; margin: 2px;">';
	                switch ($reason->reason)
	                {
	                  case 1: print 'Wrong Attachments'; break;
	                  case 2: print 'Wrong Registration'; break;
	                  case 3: print 'Wrong Tip'; break;
	                  case 4: print 'Wrong CR #'; break;
	                  case 5: print 'Wrong MVF #'; break;
	                  case 6: print 'Wrong Plate #'; break;
	                  case 7: print 'Others'; break;
	                }
	              	print '</div> ';
	              }
	              ?>
	            </div>
	          </div>

	          <div class="form-actions">
	          	<a class="btn btn-success" onclick="remarks(<?php print $sales->sid; ?>)">Unhold</a>
	            <a class="btn btn-success calculate hide">Calculate</a>
	          </div>
	        </div>

	        <div class="hide">
		        <textarea name="remarks" class="remarks"></textarea>
		        <input type="submit" name="submit">
	        </div>
        </form>

			</div>
		</div>
	</div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h3 class="modal-title">Remarks</h3>
	    </div>
	    <div class="modal-body form">
	      <div class="alert alert-error hide">
	        <button class="close" data-dismiss="alert">&times;</button>
	        <div class="error"></div>
	      </div>

	      <form action="#" id="form" class="form-horizontal" method="post">
		      <div class="form-body">
		        <div class="form-group" style="margin-bottom:15px;">
		          <label class="control-label col-md-3" style="margin-right:10px;">Remarks</label>
		          <div class="col-md-9">
		            <textarea name="remarks" class="form-control" style="width:300px;height:120px;"></textarea>
		          </div>
		        </div>
		      </div>
	      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save_remarks()" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->