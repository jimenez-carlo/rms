<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Received Transmittal</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <th><p>Branch</p></th>
            <th><p>Customer Name</p></th>
            <th><p>Engine #</p></th>
            <th><p>CR #</p></th>
            <th><p>Registration Date</p></th>
            <th><p>Topsheet</p></th>
            <th><p>Tracking #</p></th>
            <th><p>Received Date</p></th>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->bcode.' '.$row->bname.'</td>';
              print '<td>'.$row->first_name.' '.$row->last_name.'</td>';
              print '<td>'.$row->engine_no.'</td>';
              print '<td>'.$row->cr_no.'</td>';
              print '<td>'.substr($row->cr_date, 0, 10).'</td>';
              print '<td>'.$row->trans_no.'</td>';
              print '<td>'.$row->bcode.$row->sales_type.'0'.date('ymd', strtotime($row->topsheet_date)).'</td>';
              print '<td>'.$row->received_date.'</td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr>
                <td>No result.</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>