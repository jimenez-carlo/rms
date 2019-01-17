<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Fund</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <tr>
              <th><p>Company</p></th>
              <th><p>Cash in Bank</p></th>
              <th><p>Cash on Hand</p></th>
              <th><p>Cash on Check</p></th>
              <th><p>Pending at LTO</p></th>
              <th><p>Pending at ACCT</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              $key = '['.$row->fid.']';
              print '<tr>';
              print '<td>'.$row->company.'</td>';
              print '<td>'.number_format($row->fund,2,'.',',').'</td>';
              print '<td>'.number_format($row->cash_on_hand,2,'.',',').'</td>';
              print '<td>'.number_format($row->cash_on_check,2,'.',',').'</td>';
              print '<td>'.number_format($row->lto_pending,2,'.',',').'</td>';
              print '<td>'.number_format($row->for_liquidation,2,'.',',').'</td>';
              print '<td>
              	<button class="btn btn-success" onclick="modal('.$row->fid.', '.$row->company_cid.', 1)">Cash Withdrawal</button> 
                <button class="btn btn-success" onclick="modal('.$row->fid.', '.$row->company_cid.', 2)">Check Withdrawal</button> 
                <button class="btn btn-success" onclick="modal('.$row->fid.', '.$row->company_cid.', 3)">Cash Deposit</button>
              </td>';
              print '</tr>';
            }
            ?>
          </tbody>
        </table>
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
        <h3 class="modal-title">Fund</h3>
      </div>
      <div class="modal-body form">
        <div class="alert alert-error hide">
          <button class="close" data-dismiss="alert">&times;</button>
          <div class="error"></div>
        </div>
        <form action="#" id="form" class="form-horizontal">
          <div class="form-body">

            <div class="form-group check_no hide" style="margin-bottom:15px;">
              <label class="control-label" style="margin-right:10px;">Check #</label>
              <div class="controls">
                <input name="check_no" class="form-control" type="text">
              </div>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
              <label class="control-label" style="margin-right:10px;">Check Date</label>
              <div class="controls">
                <input type="text" name="check_date" class="datepicker" data-format="yyyy-mm-dd" value="<?php print date("Y-m-d"); ?>">
              </div>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
              <label class="control-label" style="margin-right:10px;">Amount</label>
              <div class="controls">
                <input name="amount" class="form-control numeric" type="text" value="0.00">
              </div>
            </div>

          </div>
        </form>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnSubmit" onclick="submit()" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
