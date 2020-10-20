<div class="row-fluid">
  <!-- block -->
  <div class="block">
    <div class="alert alert-error hide">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <div class="navbar navbar-inner block-header">
      <div class="pull-left">Repo Rerfo</div>
    </div>
    <h2>RERFO# <?php echo $rerfo_number; ?></h2>
    <table class="table">
      <thead>
        <tr>
          <td>Customer Name</td>
          <td>Customer Code</td>
          <td>Engine #</td>
          <td>AR #</td>
          <td>Amount Given</td>
          <td>Registration</td>
          <td>PNP Clearance</td>
          <td>Macro Etching</td>
          <td>Insurance</td>
          <td>Emission</td>
          <td>Date Sold</td>
          <td>Date Registered</td>
          <td>Status</td>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rerfo AS $repo): ?>
        <tr>
          <td><?php echo $repo['first_name'].' '.$repo['last_name']; ?></td>
          <td><?php echo $repo['cust_code']; ?></td>
          <td><?php echo $repo['engine_no']; ?></td>
          <td><?php echo $repo['ar_num']; ?></td>
          <td><?php echo $repo['ar_amt']; ?></td>
          <td><?php echo $repo['registration_amt']; ?></td>
          <td><?php echo $repo['pnp_clearance_amt']; ?></td>
          <td><?php echo $repo['macro_etching_amt']; ?></td>
          <td><?php echo $repo['insurance_amt']; ?></td>
          <td><?php echo $repo['emission_amt']; ?></td>
          <td><?php echo $repo['date_sold']; ?></td>
          <td><?php echo $repo['date_registered']; ?></td>
          <td><?php echo $repo['status']; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <hr>
    <h2>Miscellaneous Expense</h2>
    <?php if(isset($rerfo_misc)): ?>
    <table class="table">
      <thead>
        <tr>
          <th>Type</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rerfo_misc AS $misc): ?>
        <tr>
          <td><?php echo $misc['expense_type']; ?></td>
          <td><?php echo $misc['amount']; ?></td>
          <td><?php echo $misc['status']; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>No misc expense.</p>
    <?php endif; ?>
  </div>
</div>
