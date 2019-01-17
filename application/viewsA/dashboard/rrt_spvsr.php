<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- Sales Block -->
    <div class="block span6">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Registration Performance</div>
      </div>
      <div class="block-content collapse in">
        <div id="sales" style="height: 250px;"></div>
        <table class="table" style="margin-top:10px;margin-bottom:0px;">
          <thead>
            <tr>
              <th>LABEL</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody> 
            <?php
            $total = $new + $rejected + $pending + $nru + $registered + $closed;
            print '<tr>
              <td>RRT PENDING</td>
              <td>'.$new.'/'.$total.'</td>
              </tr>
              <tr>
              <td>REJECTED</td>
              <td>'.$rejected.'/'.$total.'</td>
              </tr>
              <tr>
              <tr>
              <td>LTO PENDING</td>
              <td>'.$pending.'/'.$total.'</td>
              </tr>
              <td>NRU</td>
              <td>'.$nru.'/'.$total.'</td>
              </tr>
              <td>REGISTERED</td>
              <td>'.$registered.'/'.$total.'</td>
              </tr>
              <tr>
              <td>CLOSED</td>
              <td>'.$closed.'/'.$total.'</td>
              </tr>';
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Topsheet Block -->
    <div class="block span6">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Accounting Process</div>
      </div>
      <div class="block-content collapse in">
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
    </div>

    <!-- Topsheet Block -->
    <div class="block span6">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Self Registration</div>
      </div>
      <div class="block-content collapse in">
        <div id="self_reg" style="height: 250px;"></div>
        <table class="table" style="margin-top:10px;margin-bottom:0px;">
          <thead>
            <tr>
              <th>LABEL</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>WITH TRANSMITTAL</td>
              <td><?php echo $sr_with; ?></td>
            </tr>
            <tr>
              <td>WITHOUT TRANSMITTAL</td>
              <td><?php echo $sr_without; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>



  </div>
</div>