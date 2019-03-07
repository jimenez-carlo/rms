<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Transmittal</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <th><p>Branch</p></th>
            <th><p>Pending Transmittal</p></th>
            <th><p>In Transit</p></th>
            <th><p>Received</p></th>
          </thead>
          <tbody>
            <?php
            $pending = $intransit = $received = 0;

            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->bcode.' '.$row->bname.'</td>';
              print '<td>'.$row->pending.'</td>';

              print ($row->intransit > 0) ? '<td><a href="transmittal/intransit/'.$row->bcode.'" target="_blank">'.$row->intransit.'</a></td>' : '<td>'.$row->intransit.'</td>';

              print ($row->received > 0) ? '<td><a href="transmittal/received/'.$row->bcode.'" target="_blank">'.$row->received.'</a></td>' : '<td>'.$row->received.'</td>';
              print '</tr>';

              $pending += $row->pending;
              $intransit += $row->intransit;
              $received += $row->received;
            }

            if (empty($table))
            {
              print '<tr>
                <td>No result.</td>
                <td></td>
                <td></td>
                </tr>';
            }
            ?>
          </tbody>
          <tfoot>
            <?php
            print '<tr>';
            print '<th>Total</th>';
            print '<th>'.$pending.'</th>';
            print '<th>'.$intransit.'</th>';
            print '<th>'.$received.'</th>';
            print '</tr>';
            ?>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>