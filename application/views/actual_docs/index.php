<div id="actual_docs" class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Actual Docs</div>
      </div>
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>Reference</th>
            <th>Date Deposited</th>
            <th>Region</th>
            <th>Company</th>
            <th>Amount</th>
            <th>Transmittal #</th>
            <th>Deposit Slip</th>
            <th>Date Received by Acctg</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($references AS $reference): ?>
          <tr>
            <td>
              <?php if($reference['transmittal_number'] === ""): ?>
              <button class="edit-actual-docs btn btn-success btn-mini" value="<?php echo $reference['transmittal_id']; ?>">
                <i class="icon-edit"></i>
              </button>
              <button
                id="save-<?php echo $reference['transmittal_id']; ?>"
                class="save-actual-docs btn btn-warning hide btn-mini"
                value="<?php echo $reference['transmittal_id']; ?>"
              >
                <i class="icon-ok icon-medium"></i>
              </button>
              <?php endif; ?>
            </td>
            <td><?php echo $reference['reference'] ?></td>
            <td><?php echo $reference['date_deposited'] ?></td>
            <td><?php echo $reference['region'] ?></td>
            <td><?php echo $reference['company'] ?></td>
            <td><?php echo $reference['amount'] ?></td>
            <td id="td-<?php echo $reference['transmittal_id']; ?>"></td>
            <td><?php echo $deposit_slip; ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
