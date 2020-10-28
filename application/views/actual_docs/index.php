<div id="actual_docs" class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Actual Docs</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <fieldset>
            <div class="control-group span5">
	      <div class="control-label">Region</div>
	      <div class="controls">
              <?php
                $region_opts = array();
                $default = 'any';
                if($_SESSION['dept_name'] === 'Regional Registration' && in_array($_SESSION['position_name'], ['RRT General Clerk', 'RRT Branch Secretary'])){
                  $region_opts['readonly'] = 'true';
                  $default = $_SESSION['region_id'];
                }
                print form_dropdown('region', $region, set_value('region', $default), $region_opts);
              ?>
              </div>
            </div>
            <div class="control-group span5">
              <div class="control-label">Status</div>
              <div class="controls">
                <?php print form_dropdown('status', array_merge(array('any' => '- Any -'), $status), set_value('status')); ?>
              </div>
            </div>
            <div class="control-group span5">
              <div class="control-label">Company</div>
              <div class="controls">
                <?php print form_dropdown('company', $company, set_value('company', 'any')); ?>
              </div>
            </div>
            <div class="control-group span5">
              <div class="control-label">Reference</div>
              <div class="controls">
                <?php print form_input('reference', set_value('reference', '')); ?>
              </div>
            </div>
            <div class="form-actions span5">
	      <input type="submit" class="btn btn-success" value="Search" name="search">
            </div>
          </fieldset>
          <div>
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
                <td>
                <?php
                  echo ($_SESSION['dept_name'] === 'Accounting' && $reference['disable_deposit_slip'] === "0" && $reference['status'] !== 'New')
                    ? form_dropdown('deposit_slip', $dep_slip_option, $reference['deposit_slip'] ?? '')
                    : $reference['deposit_slip'];
                ?>
                </td>
                <td <?php echo (isset($reference['actual_docs_id'])) ? 'id="date-incomplete-'.$reference['actual_docs_id'].'"' : ''; ?>><?php echo $reference['date_incomplete']; ?></td>
                <td <?php echo (isset($reference['actual_docs_id'])) ? 'id="date-complete-'.$reference['actual_docs_id'].'"' : ''; ?>><?php echo $reference['date_completed']; ?></td>
                <td
                  <?php echo (isset($reference['actual_docs_id'])) ? 'id="status-'.$reference['actual_docs_id'].'"' : ''; ?>
                  <?php echo 'class="status-'.$reference['transmittal_id'].'"'; ?>
                >
                  <?php echo $reference['status']; ?>
                </td>
                <!-- <td><button class="btn btn-success receive">Save</button></td> -->
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
