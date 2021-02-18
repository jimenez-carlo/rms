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
        <form class='form-horizontal' method='post'>
          <div class="row-fluid">
            <div class="form-actions">
              <input type="submit" class="btn btn-success" name="save[<?php print $sales->sid; ?>]" value="Save" onclick="return confirm('Please make sure all information are correct before proceeding. Continue?')">
            </div>
            <hr>
            <!-- Sales Block -->
            <div class="span5">
              <div class="control-group">
                <div class="control-label">Branch</div>
                <div class="controls text">
                  <?php
                  switch ($sales->voucher === "0" && $sales->electronic_payment === "0") {
                    case true:
                      print form_dropdown('branch', $branch, $sales->bcode);
                      break;
                    default:
                      print $sales->bcode.' '.$sales->bname;
                  }
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Date Sold</div>
                <div class="controls text">
                <?php
                  switch ($sales->status) {
                    case "Ongoing Transmittal":
                    case "LTO Rejected":
                    case "LTO Pending":
                      print form_input('date_sold', set_value('date_sold', $sales->date_sold), ["class" => "datepicker"]);
                      break;
                    default:
                     print $sales->date_sold;
                  }
                ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Type of Sales</div>
                <div class="controls">

                  <select id="sales_type" name="sales_type">
                  <option value="0" <?php if($sales->sales_type == 'Brand New (Cash)') echo 'selected'; ?>>Brand New (Cash)</option>
                  <option value="1" <?php if($sales->sales_type == 'Brand New (Installment)') echo 'selected'; ?>>Brand New (Installment)</option>
                  </select>
                </div>

              </div>
              <div class="control-group">
                <div class="control-label">Customer Name</div>
                <div class="controls">
                  <?php
                  print form_input('first_name', set_value('first_name', $sales->first_name), ["data-toogle"=>"tooltip", "title" => "First Name"]);
                  print form_input('middle_name', set_value('middle_name', $sales->middle_name), ["data-toogle"=>"tooltip", "title" => "Middle Name"]);
                  print form_input('last_name', set_value('last_name', $sales->last_name), ["data-toogle"=>"tooltip", "title" => "Last Name"]);
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Customer Code</div>
                <div class="controls">
                  <?php
                  print form_input('cust_code', set_value('cust_code', $sales->cust_code));
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Engine #</div>
                <div class="controls">
                  <?php
                  print form_input('engine_no', set_value('engine_no', $sales->engine_no));
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Chassis #</div>
                <div class="controls">
                  <?php
                  print form_input('chassis_no', set_value('chassis_no', $sales->chassis_no));
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">SI #</div>
                <div class="controls">
                  <?php
                  print form_input('si_no', set_value('si_no', $sales->si_no));
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">AR #</div>
                <div class="controls">
                  <?php
                  print form_input('ar_no', set_value('ar_no', $sales->ar_no));
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Amount Given</div>
                <div class="controls">
                  <?php
                  print form_input('amount', set_value('amount', $sales->amount));
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Registration Type</div>
                <div class="controls">
                  <?php
                  $registration_type = array(
                    'Free Registration' => 'Free Registration',
                    'Regn. under NIA' => 'Regn. under NIA',
                    'Regular Regn. Paid' => 'Regular Regn. Paid',
                    'Self Registration' => 'Self Registration',
                    'With Regn. Subsidy' => 'With Regn. Subsidy'
                  );
                  print form_dropdown('registration_type', $registration_type, set_value('registration_type', $sales->registration_type));
                  ?>
                </div>
              </div>
              <div class="control-group">
                <div class="control-label">Insurance</div>
                <div class="controls text"><?php print $sales->insurance; ?></div>
              </div>
              <div class="control-group">
                <div class="control-label">Tip</div>
                <div class="controls text"><?php print $sales->tip; ?></div>
              </div>
              <div class="control-group">
                <div class="control-label">Registration</div>
                <div class="controls text"><?php print $sales->registration; ?></div>
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
                                            $path = '/rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/'.$file;
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
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function(){
  $(document).ready(function(){
    $('.controls.text').each(function(){
      if (!$(this).text()) $(this).text('-');
    });
  });
})
</script>
