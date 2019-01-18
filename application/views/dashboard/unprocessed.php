<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Unprocessed DIY</div>
      </div>
      <div class="block-content collapse in">
        <div class="span4"></div>
        <!-- Unprocessed Block -->
        <div class="span4">
          <div id="unprocessed" style="height: 350px;"></div>
          <table class="table" style="margin-top:10px;margin-bottom:0px;">
            <thead>
              <tr>
                <th>LABEL</th>
                <th>TOTAL</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>NO PNP</td>
                <td><?php echo $pnp_count; ?></td>
              </tr>
              <tr>
                <td>NO AR</td>
                <td><?php echo $ar_count; ?></td>
              </tr>
              <tr>
                <td>NO SI</td>
                <td><?php echo $si_count; ?></td>
              </tr>
              <tr>
                <td>NO INSURANCE</td>
                <td><?php echo $insurance_count; ?></td>
              </tr>
              <tr>
                <td>PENDING</td>
                <td><?php echo $pending_count; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="span4"></div>
      </div>
    </div>
  </div>
</div>