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
            <th><p>Tracking #</p></th>
            <th><p>Branch</p></th>
            <th><p>Date Sent</p></th>
            <th><p>Sent By</p></th>
            <th><p></p></th>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->branch->b_code.' '.$row->branch->name.'</td>';
              print '<td>'.$row->trans_no.'</td>';
              print '<td>'.$row->date.'</td>';
              print '<td>'.$row->user->firstname.' '.$row->user->lastname.'</td>';
              print '<td><a href="view/'.$row->tid.'" class="btn btn-success">View transmittal</a></td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr>
                <td>No result.</td>
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