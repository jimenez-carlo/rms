<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid form-horizontal">
        <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Customer Sales # <?php print $sales->sid; ?></div>
      </div>
      <div class="block-content collapse in">
        <?php if ($_SESSION['position'] == -2) { /**special site admin**/ ?>
        <div class="row-fluid">
          <form class='form-horizontal' method='post' action="<?php echo base_url(); ?>sales/edit">
            <div class="form-actions">
              <input type="submit" class="btn btn-success" name="edit[<?php print $sales->sid; ?>]" value="Edit">
            </div>
          </form>
        </div>
        <?php } ?>

        <div class="row-fluid">
          <!-- Sales Block -->
          <div class="span5">
            <div class="control-group">
              <div class="control-label">Branch</div>
              <div class="controls text"><?php print $sales->bcode.' '.$sales->bname; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Date Sold</div>
              <div class="controls text"><?php print $sales->date_sold; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Type of Sales</div>
              <div class="controls text"><?php print $sales->sales_type; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Customer Name</div>
              <div class="controls text"><?php print $sales->first_name.' '.$sales->last_name; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Customer Code</div>
              <div class="controls text"><?php print $sales->cust_code; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Engine #</div>
              <div class="controls text"><?php print $sales->engine_no; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Chassis #</div>
              <div class="controls text"><?php print $sales->chassis_no; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">SI #</div>
              <div class="controls text"><?php print $sales->si_no; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">AR #</div>
              <div class="controls text"><?php print $sales->ar_no; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Amount Given</div>
              <div class="controls text"><?php print $sales->amount; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Registration Type</div>
              <div class="controls text"><?php print $sales->registration_type; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Registration</div>
              <div class="controls text"><?php print $sales->registration; ?></div>
            </div>
            <div class="control-group form-inline">
              <div class="control-label">Penalty</div>
              <div class="controls text"><?php print $sales->penalty; ?>
                <?php if(in_array($_SESSION['dept_name'], ['Regional Registration', 'Accounting', 'Treasury'])): ?>
                <label>| RIC <?php print ($sales->is_penalty_for_ric === '1') ? 'Yes' : 'No'; ?></label>
                <?php endif; ?>
              </div>
            </div>
            <div class="control-group">
              <div class="control-label">Tip</div>
              <div class="controls text"><?php print $sales->tip; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Insurance</div>
              <div class="controls text"><?php print $sales->insurance; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Registered Date</div>
              <div class="controls text"><?php print substr($sales->cr_date, 0, 10); ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">CR #</div>
              <div class="controls text"><?php print $sales->cr_no; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">MV File #</div>
              <div class="controls text"><?php print $sales->mvf_no; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Plate #</div>
              <div class="controls text"><?php print $sales->plate_number; ?></div>
            </div>
          </div>

          <!-- Status Block -->
          <div class="span7">
            <div class="control-group">
              <div class="control-label">Status</div>
              <div class="controls text"><?php print $sales->status; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Last updated on</div>
              <div class="controls text"><i><?php print $sales->last_update; ?></i></div>
            </div>
            <div class="control-group">
              <div class="control-label">LTO Transmittal</div>
              <div class="controls text"><?php print $sales->transmittal_date; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Pending at LTO</div>
              <div class="controls text"><?php print $sales->pending_date; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">ORCR Encoded on</div>
              <div class="controls text"><?php print $sales->registration_date; ?></div>
            </div>

            <hr>

            <?php
            if (!empty($sales->files))
            {
              foreach ($sales->files as $file)
              {
                $path = base_url().'rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/'.$file;
                print '<div class="attachment" style="position:relative">';
                print '<img src="'.$path.'" style="margin:1em; border:solid">';
                print '</div>';
              }
            }
            else
            {
                print '<div class="control-group"><div class="controls text">No attachments.</div></div>';
            }
            ?>
          </div>
        </div>
                        </div>
                </div>
        </div>
</div>

<script type="text/javascript">
$(function(){
  $(document).ready(function(){
    $('.controls').each(function(){
      if (!$(this).text()) $(this).text('-');
    });
  });
})
</script>
