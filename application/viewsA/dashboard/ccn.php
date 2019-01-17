<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">

    <!-- Repo Block 
    <div class="block span6">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo Sales</div>
      </div>
      <div class="block-content collapse in">
        <div id="repo" style="height: 250px;"></div>
        <table class="table" style="margin-top:10px;">
          <thead>
            <tr>
              <th>LABEL</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>BRANCH PENDING</td>
              <td><?php echo $pending; ?></td>
            </tr>
            <tr>
              <td>LTO PENDING</td>
              <td><?php echo $lto_pending; ?></td>
            </tr>
            <tr>
              <td>REGISTERED</td>
              <td><?php echo $registered; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>-->

    <!-- Brand New Block -->
    <div class="block span6">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Brand New Sales</div>
      </div>
      <div class="block-content collapse in">
        <div id="bnew" style="height: 250px;"></div>
        <table class="table" style="margin-top:10px;margin-bottom:0px;">
          <thead>
            <tr>
              <th>LABEL</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>REJECTED</td>
              <td><?php echo $bnew_rejected; ?></td>
            </tr>
            <tr>
              <td>LTO PENDING</td>
              <td><?php echo $bnew_pending; ?></td>
            </tr>
            <tr>
              <td>REGISTERED</td>
              <td><?php echo $bnew_registered; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Rerfo Block -->
    <div class="block span6">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Brand New ORCR</div>
      </div>
      <div class="block-content collapse in">
        <div id="orcr" style="height: 250px;"></div>
        <table class="table" style="margin-top:10px;margin-bottom:37px;">
          <thead>
            <tr>
              <th>LABEL</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>TRANSMITTED</td>
              <td><?php echo $transmitted; ?></td>
            </tr>
            <tr>
              <td>RECEIVED</td>
              <td><?php echo $received; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
