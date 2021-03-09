<div class="row-fluid">
  <!-- block -->
  <div class="block">
    <div class="alert alert-error hide">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <div class="navbar navbar-inner block-header">
      <div class="pull-left">Repo View CA Batch</div>
    </div>
    <h3>Reference# <?php echo $reference; ?></h3>
    <?php echo $batch; ?>
    <hr>
    <h3>Miscellaneous Expense</h3>
    <?php if(isset($misc_expenses)): ?>
    <table class="table">
      <thead>
        <tr>
          <th>Type</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach(json_decode($misc_expenses, 1) AS $misc): ?>
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
