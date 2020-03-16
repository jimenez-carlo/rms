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

            <table id="table-engine" class="table">
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
                  print '<td><a class="a-engine" href="'.base_url().'sales/view/'.$sales->sid.'" target="_blank">'.$sales->engine_no.'</a></td>';
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
  var count = 0;
  var LPID = <?php echo $lpid; ?>;
  $(function(){
    $('a.add_more').click(function(){
      var tfoot = $(this).closest('tfoot');
      var valid = 1;

      $('.a-engine').each(function(){
        if ($(this).text() == $('#new_entry').val()) {
          valid = 0;
          return false;
        }
      });

      if (valid) {
        if (count !== 0) {
          var engine_number = $('#new_entry').val();
          var engine_status = 'LTO Pending';
          $.ajax({
            url: "<?php echo base_url(); ?>api",
            method: "get",
            data: {
              "engine": engine_number,
              "status": engine_status,
              "payment_method": "EPP",
              "region": "<?php echo $region; ?>",
              "company_id": "<?php echo $payment->company; ?>"
            },
            success: function(result) {
              var sales = JSON.parse(result);
              sales.forEach(function(sale) {
                if (sale.lto_payment == LPID) {
                  console.log('Engine# '+sales[0].engine_no+' is already exist.');
                } else if(sale.status_name != engine_status) {
                  console.log('Engine# '+sales[0].engine_no+' status is not '+engine_status+'.');
                } else {
                    $('#table-engine tbody').append(' \
                      <tr> \
                        <td>'+sale.bcode+' '+sale.bname+'</td> \
                        <td>'+sale.last_name+' '+sale.first_name+'</td> \
                        <td>'+sale.cust_code+'</td> \
                        <td> \
                          <a class="a-engine" href="<?php echo base_url();?>sales/view/473821" target="_blank">'+sale.engine_no+'</a> \
                          <input type="hidden" name="engine_no[]" value="'+sale.engine_no+'"> \
                        </td> \
                        <td> \
                          <!-- <input type="checkbox" name="remove[]" value="'+sale.sid+'"> --> \
                        </td> \
                      </tr> \
                    ');
                }
              });
            }
          });
          $('#new_entry').parent().parent().remove();
        }
        count++;
        tfoot.prepend('<tr><td>Engine #</td><td colspan="4"><input id="new_entry" type="text" name="engine_no[]"></td></tr>');
      }
    });
  });
</script>
