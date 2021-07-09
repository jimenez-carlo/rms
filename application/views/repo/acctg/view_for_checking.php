<form id="FormSummary" method="POST">
<input type="hidden" name="repo_batch_id"  value="<?php echo $record->repo_batch_id; ?>">
<table class="table">
  <thead>
    <tr>
      <th></th>
      <th>#</th>
      <th>Branch</th>
      <th>Date Sold</th>
      <th>Engine #</th>
      <th>AR #</th>
      <th>AR Amt</th>
      <th>Status</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  
  <?php $ctr = 0; ?>
    <?php foreach ($sales as $res) { ?>
      <tr id="tr_sales_id_<?php echo $res['repo_sales_id']; ?>">
        <td><input type="checkbox" name="sales[]" id="sales_id-<?php echo $res['repo_sales_id']; ?>" value="<?php echo $res['repo_sales_id']; ?>" data-amt="<?php echo $res['sales_amt']; ?>" data-selectable="true"></td>
        <td><?php echo $ctr = $ctr+1; ?></td>
        <td><?php echo $res['bcode']." ".$res['bname'] ?></td>
        <td><?php echo $res['date_sold']; ?></td>
        <td><?php echo $res['engine_no']; ?></td>
        <td><?php echo $res['ar_num']; ?></td>
        <td><?php echo number_format($res['ar_amt'],2); ?></td>
        <td><?php echo $res['status_name']; ?></td>
        <td><button class="btn btn-success view" style="float:right;" type="button" value="<?php echo $res['repo_sales_id']; ?>" onclick="view_sales(<?php echo $res['repo_sales_id']; ?>,'<?php echo $res['engine_no']; ?>','<?php echo $repo_batch;?>')">View</button></td>
      </tr>
    <?php } ?>
  </tbody>
</table>
<table class="table">
  <thead>
    <tr>
      <th></th>
      <th>#</th>
      <th>OR Date</th>
      <th>OR No.</th>
      <th>Expense Type</th>
      <th>Amount</th>
      <th>Status</th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  
  <?php $ctr = 0; ?>
  <?php foreach ($misc as $res) { ?>
    <tr id="tr_misc_id_<?php echo $res['mid']; ?>" class="">
      <?php if ($res['status_id'] == 5) { ?>
        <td></td>
      <?php }else{ ?>
      <td><input type="checkbox" name="misc[]" value="<?php echo $res['mid']; ?>" id="misc_id-<?php echo $res['mid']; ?>" data-selectable="true" data-amt="<?php echo $res['amount']; ?>">
      </td>
      <?php } ?>
      <td><?php echo $ctr = $ctr+1; ?></td>
      <td><?php echo $res['or_date']; ?></td>
      <td><?php echo $res['or_no'] ?></td>
      <td><?php echo $res['type'] ?></td>
      <td><?php echo number_format($res['amount'],2); ?></td>
      <td <?php echo ($res['status_id'] == 5) ? 'style="color:red;font-weight:bold"': ""; ?>><?php echo $res['status_name']; ?></td>
      <td></td>
      <td><button type="button" class="btn btn-success view" style="float:right" onclick="view_misc(<?php echo $res['mid']; ?>,'<?php echo $res['type'] ?>','<?php echo $repo_batch;?>')">View</button>
      </td>
    </tr>
  <?php } ?>
    <tr>
      <td class="brdrt" colspan="8"></td>
      <td class="brdrt bld" colspan="1">Total Amount</td>
    </tr>
    <tr>
      <td class="bld" colspan="8">Batch</td>
      <td class="bld" colspan="1">₱ <?php echo number_format(intval($record->amount),2); ?></td>
    </tr>
    <tr>
      <td class="bld" colspan="8">Liquidated</td>
      <td class="bld" colspan="1">₱ <?php echo number_format(intval($liquidated_amount),2); ?></td>
    </tr>
    <tr>
      <td class="bld" colspan="8">Checked</td>
      <td class="bld" colspan="1">₱ <?php echo number_format($checked_amount,2); ?></td>
    </tr>
    <tr>
      <td class="bld" colspan="8">Balance</td>
      <td class="bld bal" colspan="1">₱ <?php echo number_format(intval($record->amount)-intval($liquidated_amount),2); ?></td>
    </tr>
    <tr>
      <td class="bld clr-rd al" colspan="3">Balance for upload must not be negative.</td>
      <td colspan="4"></td>
      <td class="bld" colspan="1">Expense</td>
      <td class="bld exp_display clr-rd" colspan="1">₱ 0.00</td>
    </tr>
    <tr>
      <td class="brdrb bld" colspan="9"></td>
    </tr>
  </tbody>
</table>
<button type="submit" id="preview-summary" name="preview-summary" class="btn btn-success" disabled="">Preview Summary</button>
<!-- id = preview-summary name="preview" -->
</form>