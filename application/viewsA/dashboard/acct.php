<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">

    <!-- Topsheet Block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">For Checking</div>
      </div>
      <div class="block-content collapse in">
        <div class="span3"></div>
        <div class="span6">
          <div id="topsheet" style="height: 250px;"></div>
          <table class="table" style="margin-top:10px;margin-bottom:0px;">
            <thead>
              <tr>
                <th>LABEL</th>
                <th>TOTAL</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>UNPROCESSED</td>
                <td><?php echo $ts_unprocessed; ?></td>
              </tr>
              <tr>
                <td>INCOMPLETE</td>
                <td><?php echo $ts_incomplete; ?></td>
              </tr>
              <tr>
                <td>FOR SAP UPLOAD</td>
                <td><?php echo $ts_sap_upload; ?></td>
              </tr>
              <tr>
                <td>DONE</td>
                <td><?php echo $ts_done; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="span3"></div>
      </div>

    </div>


  </div>
</div>
    <!-- Batch Block 
    <div class="block span6">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">For Processing</div>
      </div>
      <div class="block-content collapse in">
        <div id="batch" style="height: 250px;"></div>
        <table class="table" style="margin-top:10px;">
          <thead>
            <tr>
              <th>LABEL</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>FOR SAP UPLOAD</td>
              <td><?php echo $b_sap; ?></td>
            </tr>
            <tr>
              <td>FOR CA OFFSET</td>
              <td><?php echo $b_offset; ?></td>
            </tr>
            <tr>
              <td>FOR VOUCHER</td>
              <td><?php echo $b_voucher; ?></td>
            </tr>
            <tr>
              <td>DONE</td>
              <td><?php echo $b_done; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    -->
