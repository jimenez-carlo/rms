<div id="actual_docs" class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Actual Docs</div>
      </div>
      <table class="table">
        <thead>
          <tr>
            <?php if($_SESSION['dept_name'] === 'Regional Registration'): ?>
            <th></th>
            <?php endif; ?>
            <th>Reference</th>
            <th>Date Deposited</th>
            <th>Region</th>
            <th>Company</th>
            <th>Amount</th>
            <th>Transmittal #</th>
            <th>Deposit Slip</th>
            <th>Date Incomplete</th>
            <th>Date Completed</th>
            <th>Status</th>
            <!-- <th></th> -->
          </tr>
        </thead>
        <tbody>
          <?php foreach($references AS $reference): ?>
          <tr <?php echo ($reference['actual_docs_id'] !== NULL) ? 'id="'.$reference['actual_docs_id'].'"' : ''; ?>>
            <?php if($_SESSION['dept_name'] === 'Regional Registration'): ?>
            <td>
              <?php if($reference['transmittal_number'] === NULL || $reference['status'] === 'Incomplete'): ?>
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
              <input
                type="hidden"
                id="id-<?php echo $reference['transmittal_id']; ?>"
                value="<?php echo $reference['id']; ?>"
              >
              <input
                type="hidden"
                id="pt-<?php echo $reference['transmittal_id']; ?>"
                value="<?php echo $reference['payment_type']; ?>"
              >
              <?php endif; ?>
            </td>
            <?php endif; ?>
            <td><?php echo $reference['reference'] ?></td>
            <td><?php echo $reference['date_deposited'] ?></td>
            <td><?php echo $reference['region'] ?></td>
            <td><?php echo $reference['company'] ?></td>
            <td><?php echo $reference['amount'] ?></td>
            <td id="td-<?php echo $reference['transmittal_id']; ?>">
              <?php echo ($reference['transmittal_number'] !== "") ? $reference['transmittal_number'] : "" ; ?>
            </td>
            <?php
              $dep_slip_extra =
              ($_SESSION['dept_name'] === 'Regional Registration' || $reference['deposit_slip'] === 'Original')
              ? ['disabled'=> 'disabled'] : [];
            ?>
            <td><?php echo form_dropdown('deposit_slip', $dep_slip_option, $reference['deposit_slip'], $dep_slip_extra); ?></td>
            <td id="date-incomplete-<?php echo $reference['actual_docs_id']; ?>"><?php echo $reference['date_incomplete']; ?></td>
            <td id="date-complete-<?php echo $reference['actual_docs_id']; ?>"><?php echo $reference['date_completed']; ?></td>
            <td <?php echo ($_SESSION['dept_name'] === 'Accounting') ? 'id="status-'.$reference['actual_docs_id'].'"' : ''; ?>><?php echo $reference['status']; ?></td>
            <!-- <td><button class="btn btn-success receive">Save</button></td> -->
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
