<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid form-horizontal">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">LTO Payment # <?php print $payment->lpid; ?></div>
      </div>
      <div class="block-content collapse in">
        <div class="row-fluid">
          <div class="span5">
            <?php if ($payment->status == 'Pending') { ?>
            <div class="control-group">
              <div class="control-label"></div>
              <div class="controls text"><a href=<?php print base_url()."lto_payment/edit/".$payment->lpid; ?> class="btn btn-success">Update details</a></div>
            </div>
            <?php } ?>
            <div class="control-group">
              <div class="control-label">Region</div>
              <div class="controls text"><?php print $payment->region.' '.$payment->company; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Date</div>
              <div class="controls text"><?php print (empty($payment->ref_date)) ? '-' : $payment->ref_date; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Payment Reference #</div>
              <div class="controls text"><?php print (empty($payment->reference)) ? '-' : $payment->reference; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Amount</div>
              <div class="controls text"><?php print (empty($payment->amount)) ? '-' : number_format($payment->amount, 2, '.', ','); ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Payment Status</div>
              <div class="controls text"><?php print $payment->status; ?></div>
            </div>
            <hr>
            <div class="control-group">
              <div class="control-label">Document #</div>
              <div class="controls text"><?php print (empty($payment->doc_no)) ? '-' : $payment->doc_no.'<br><i>on '.$payment->doc_date.'</i>'; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Debit Memo #</div>
              <div class="controls text"><?php print (empty($payment->dm_no)) ? '-' : $payment->dm_no.'<br><i>on '.$payment->dm_date.'</i>'; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Payment Confirmation #</div>
              <div class="controls text"><?php print (empty($payment->confirmation)) ? '-' : $payment->confirmation.'<br><i>on '.$payment->dm_date.'</i>'; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Date Liquidated</div>
              <div class="controls text"><?php print (empty($payment->close_date)) ? '-' : $payment->close_date; ?></div>
            </div>
          </div>

          <div class="span7">
            <!-- <div class="control-group">
              <div class="control-label">Screenshot</div>
              <div class="controls text"><?php print (empty($payment->screenshot)) ? '-' : '<a href="/rms_dir/lto_screenshot/'.$payment->lpid.'/'.$payment->screenshot.'" target="_blank">'.$payment->screenshot.'</a>'; ?></div>
            </div> -->
            <div class="control-group">
              <div class="control-label">Receipt</div>
              <div class="controls text"><?php print (empty($payment->receipt)) ? '-' : '<a href="'.base_url().'rms_dir/lto_receipt/'.$payment->lpid.'/'.$payment->receipt.'" target="_blank">'.$payment->receipt.'</a>'; ?></div>
            </div>

            <hr>

            <div class="control-group">
              <div class="control-label"></div>
              <div class="controls text"><a href="<?php print base_url().'lto_payment/print_batch/'.$payment->lpid?>" target="_blank" class="btn btn-success">Print</a></div>
            </div>

            <table class="table">
              <thead>
                <tr>
                  <th><p>#</p></th>
                  <th><p>Branch</p></th>
                  <th><p>Customer Name</p></th>
                  <th><p>Customer Code</p></th>
                  <th><p>Engine #</p></th>
                  <th><p>Chassis #</p></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $ctr = 0;
                foreach ($payment->sales as $sales) {
                  $ctr++;
                  print '<tr>';
                  print '<td>'.$ctr.'</td>';
                  print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                  print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                  print '<td>'.$sales->cust_code.'</td>';
                  print '<td><a href="'.base_url().'sales/view/'.$sales->sid.'" target="_blank">'.$sales->engine_no.'</a></td>';
                  print '<td>'.$sales->chassis_no.'</td>';
                  print '</tr>';
                }
                ?>
              </tbody>
            </table>
          </div>

        </div>
			</div>
		</div>
	</div>
</div>
