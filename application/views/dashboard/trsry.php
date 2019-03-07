<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- Topsheet Block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Accounting Process</div>
      </div>
      <div class="block-content collapse in">
        <div class="span4"></div>
        <div class="span4">
          <div id="batch" style="height: 350px;"></div>
          <table class="table" style="margin-top:10px;margin-bottom:0px;">
            <thead>
              <tr>
                <th>LABEL</th>
                <th>TOTAL</th>
              </tr>
            </thead>
            <tbody>
                <td>FOR CHECK ISSUANCE</td>
                <td><?php echo $b_issuance; ?></td>
              </tr>
              <tr>
                <td>FOR MANAGEMENT APPROVAL</td>
                <td><?php echo $b_approval; ?></td>
              </tr>
              <tr>
                <td>FOR CHECK DEPOSIT</td>
                <td><?php echo $b_deposit; ?></td>
              </tr>
              <tr>
                <td>DEPOSITED</td>
                <td><?php echo $b_done; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="span4"></div>
      </div>
    </div>

  </div>
</div>