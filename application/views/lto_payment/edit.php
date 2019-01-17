<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Add New Batch</div>
      </div>
      <div class="block-content collapse in">

        <form class="form-horizontal" method="post" enctype="multipart/form-data">
          <fieldset class="span5">
            <div class="control-group">
              <div class="control-label">Region</div>
              <div class="controls">
                <?php print $region; ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Company</div>
              <div class="controls">
                <?php print form_dropdown('company', $company, set_value('company', $payment->company)); ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Payment Reference #</div>
              <div class="controls">
                <?php print form_input('reference', set_value('reference', $payment->reference)); ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Date</div>
              <div class="controls">
                <?php print form_input('ref_date', set_value('ref_date', $payment->ref_date), array('class' => 'datepicker')); ?>
              </div>
            </div>

            <div class="control-group">
              <div class="control-label">Amount</div>
              <div class="controls">
                <?php print form_input('amount', set_value('amount', $payment->amount)); ?>
              </div>
            </div>

            <div class="form-actions">
              <input type="submit" name="save" value="Save" class="btn btn-success" onclick="return confirm('Please make sure all information are correct before proceeding. Continue?')">
            </div>
          </fieldset>

          <div class="span6">
            <!-- <div class="control-group">
              <div class="control-label">Screenshot</div>
              <div class="controls">
                <a href="<?php print '/rms_dir/lto_screenshot/'.$payment->lpid.'/'.$payment->screenshot; ?>" target="_blank"><?php print set_value('screenshot', $payment->screenshot); ?></a><br>
                <input type="file" name="screenshot" class="input-file uniform_on">
                <br><b>Required file format: PDF</b>
                <br><b>File must not exceed 1MB</b>
              </div>
            </div> -->

            <hr>

            <table class="table">
              <thead>
                <tr>
                  <th><p>Branch</p></th>
                  <th><p>Customer Name</p></th>
                  <th><p>Customer Code</p></th>
                  <th><p>Engine #</p></th>
                  <th><p>Remove</p></th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($payment->sales as $sales) {
                  print '<tr>';
                  print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                  print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                  print '<td>'.$sales->cust_code.'</td>';
                  print '<td><a href="/sales/view/'.$sales->sid.'" target="_blank">'.$sales->engine_no.'</a></td>';
                  print '<td><input type="checkbox" name="remove[]" value="'.$sales->sid.'"></td>';
                  print '</tr>';
                }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="5"><a class="add_more btn btn-success">Add Engine</a></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </form>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
  $(function(){
    $('a.add_more').click(function(){
      var tfoot = $(this).closest('tfoot');

      var valid = 1;
      tfoot.find('input[name^=engine_no]').each(function(){
        if (!$(this).val()) valid = 0;
      });

      if (valid) {
        tfoot.prepend('<tr><td>Engine #</td><td colspan="4"><input type="text" name="engine_no[]"></td></tr>');
      }
    });
  });
</script>