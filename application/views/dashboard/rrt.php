<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">

    <!-- Rerfo Block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">ORCR Branch Transmittal</div>
      </div>
      <div class="block-content collapse in">
        <div class="span3"></div>
        <div class="span6">
          <div id="rerfo" style="height: 250px;"></div>
          <table class="table" style="margin-top:10px;margin-bottom:0px;">
            <tbody>
              <tr>
                <td>FOR TRANSMITTAL</td>
                <td><?php echo $rrt_pending; ?></td>
              </tr>
              <tr>
                <td>TRANSMITTED</td>
                <td><?php echo $transmitted; ?></td>
              </tr>
              <tr>
                <td>RECEIVED</td>
                <td><?php echo $received; ?></td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <th>TOTAL</th>
                <th><?php print ($rrt_pending + $transmitted + $received); ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="span3"></div>
      </div>
    </div>

  </div>
</div>
