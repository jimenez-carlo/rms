<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid form-horizontal">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Return Fund # <?php print $return->rfid; ?></div>
      </div>
      <div class="block-content collapse in">
        <div class="row-fluid">
          <div class="span5">
            <div class="control-group">
              <div class="control-label">Reference #</div>
              <div class="controls text"><?php print $return->reference; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Amount</div>
              <div class="controls text">
              <?php
                echo ($return->status === 'Wrong Amount' && $_SESSION['dept_name'] === 'Regional Registration')
                  ? form_input(['id' => 'input-amount', 'value' => $return->amount]) : $return->amount;
              ?>
              </div>
            </div>
            <div class="control-group">
              <div class="control-label">Status</div>
              <div class="controls text"><?php print $return->status; ?></div>
            </div>
            <div class="control-group">
              <div class="control-label">Date Liquidated</div>
              <div class="controls text"><?php print (empty($return->liq_date)) ? '-' : $return->liq_date; ?></div>
            </div>
            <?php if (in_array($return->status, array('For Liquidation')) && $_SESSION['position_name'] === 'Accounts Payable Clerk') : ?>
            <form method="post" class="form-horizontal">
              <div class="form-actions">
                <input type="submit" name="liquidate" value="Liquidate" class="btn btn-success" onclick="return confirm('This action cannot be undone: Liquidate. Continue?')">
                <button id="rf-disapprove" type="button" class="btn btn-danger">Disapprove</button>
              </div>
            </form>
            <div class="control-group return_fund_disapprove hide">
              <form id="return-disapprove-form" action="../disapprove/<?php echo $return->rfid; ?>" method="post">
                <?php echo form_input(['name' => 'amount',  'value' => $return->amount, 'type' => 'hidden']); ?>
                <?php echo form_input(['name' => 'fund_id',  'value' => $return->fund_id, 'type' => 'hidden']); ?>
                <div class="controls">
                <?php
                  $status = array(
                    '2' => 'Wrong Company Deposited',
                    '3' => 'Wrong Amount',
                    '4' => 'Double Upload',
                    '5' => 'Wrong Attached Deposit Slip'
                  );
                  echo form_dropdown('ret_dis_status', $status, '1', array('class' => 'span6'));
                ?>
                </div>
              </form>
            </div>
            <div class="control-group return_fund_disapprove hide">
              <div class="controls">
                <button id="return-fund-save-disapprove" class="btn btn-warning">Save Disapprove</button>
              </div>
            </div>
            <?php elseif($_SESSION['position_name'] === 'RRT Supervisor') : ?>
            <?php
              switch ($return->status) {
                case 'Double Upload':
                case 'Wrong Company Deposited':
                case 'Wrong Attached Deposit Slip':
                  echo <<<FORM
                    <form method="post" action="../delete/{$return->rfid}" class="form-horizontal">
                      <div class="form-actions">
                        <input type="submit" value="Delete" class="btn btn-warning"
                        onclick="return confirm('This action cannot be undone: Delete. Continue?')">
                      </div>
                    </form>
FORM;
                  break;
                case 'Wrong Amount':
                  echo <<<FORM
                    <form id="form-correct-amount" class="form-horizontal" method="post">
                      <input type="hidden" name="amount" value"">
                      <div class="form-actions">
                        <button id="save-correct-amount" class="btn btn-success">Save</button>
                      </div>
                    </form>
FORM;
                  echo '<div class="form-actions">';
                  echo   '';
                  echo '</div>';
                  break;
              }
            ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <img class="img-rounded" <?php echo 'src="'.base_url().'rms_dir/deposit_slip/'.$return->rfid.'/'.$return->slip.'"  alt="'.$return->slip.'"'; ?>>
</div>
