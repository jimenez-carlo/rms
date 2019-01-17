<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid hide">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Transmittal</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <th><p>Transaction #</p></th>
            <th><p>Company</p></th>
            <th><p>Registration Date</p></th>
            <th><p></p></th>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->trans_no.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td>'.$row->date.'</td>';

              if ($row->transmittal) print '<td><a href="transmittal/sprint/'.$row->tid.'" class="btn btn-success">Print transmittal</a></td>';
              else print '<td><a href="transmittal/generate/'.$row->tid.'" class="btn btn-success">Create transmittal</a></td>';
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

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Transmittal</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <th><p>Transaction #</p></th>
            <th><p>Company</p></th>
            <th><p>Registration Date</p></th>
            <th><p></p></th>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->trans_no.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td>'.$row->date.'</td>';
              print '<td><a href="transmittal/generate/'.$row->tid.'" class="btn btn-success">Create transmittal</a></td>';
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