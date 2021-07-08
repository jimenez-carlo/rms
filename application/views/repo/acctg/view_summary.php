<form id="FormSummarySubmit" method="POST">
<input type="hidden" name="repo_batch_id"  value="<?php echo $record->repo_batch_id; ?>">
<input type="hidden"  name="sales"  value="<?php echo $sales_ids; ?>">
<input type="hidden"   name="misc"  value="<?php echo $misc_ids; ?>">
<table class="table">
  <thead>
    <tr>
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
    <?php if(!empty($sales)){ ?>
      <?php $ctr = 0; ?>
    <?php foreach ($sales as $res) { ?>
      <tr id="tr_sales_id_<?php echo $res['repo_sales_id']; ?>">
        <td><?php echo $ctr = $ctr+1; ?></td>
        <td><?php echo $res['bcode']." ".$res['bname'] ?></td>
        <td><?php echo $res['date_sold']; ?></td>
        <td><?php echo $res['engine_no']; ?></td>
        <td><?php echo $res['ar_num']; ?></td>
        <td><?php echo number_format($res['ar_amt'],2); ?></td>
        <td><?php echo $res['status_name']; ?></td>   </tr>
      <?php } ?>
      <?php } ?>
  </tbody>
</table>
<table class="table">
  <thead>
    <tr>
      <th>#</th>
      <th colspan="1">OR Date</th>
      <th colspan="1">OR No.</th>
      <th colspan="2">Expense Type</th>
      <th colspan="2">Amount</th>
      <th colspan="2">Status</th>
    </tr>
  </thead>
  <tbody>
  <?php $ctr = 0; ?>
  <?php if(!empty($misc)){ ?>
  <?php foreach ($misc as $res) { ?>
    
    <tr id="tr_misc_id_<?php echo $res['mid']; ?>" class="">
      <td><?php echo $ctr = $ctr+1; ?></td>
      <td colspan="1"><?php echo $res['or_date']; ?></td>
      <td colspan="1"><?php echo $res['or_no'] ?></td>
      <td colspan="2"><?php echo $res['type'] ?></td>
      <td colspan="2"><?php echo number_format($res['amount'],2); ?></td>
      <td colspan="2" <?php echo ($res['status_id'] == 5) ? 'style="color:red;font-weight:bold"': ""; ?>><?php echo $res['status_name']; ?></td>
    </tr>
    <?php } ?>
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
      <td class="bld" colspan="1">₱ 0.00</td>
    </tr>
    <tr>
      <td class="bld" colspan="8">Checked</td>
      <td class="bld" colspan="1">₱ <?php echo number_format($checked_amount,2); ?></td>
    </tr>
    <tr>
      <td class="bld" colspan="8">Balance</td>
      <td class="bld bal" colspan="1">₱ <?php echo number_format(intval($record->amount),2); ?></td>
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
<button type="submit" class="btn btn-success" >Save</button>
<button type="button" class="btn btn-success" >Cancel</button>
</form>
