<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">ORCR Liquidation</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <tr>
              <th><p>Date</p></th>
              <th><p>Reference</p></th>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th><p># of Units</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->date.'</td>';
              print '<td>'.$row->reference.'</td>';
              print '<td>'.$region[$row->fund].'</td>';
              print '<td>'.$company[$row->company].'</td>';
              print '<td>'.$row->sales.'</td>';
              print '<td><a href="/orcr_liquidation/liquidate/'.$row->vid.'" class="btn btn-success">Liquidate</a></td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr><td>No result.</td></tr>';
            }
            ?>
          </tbody>
        </table>
			</div>
		</div>
  </div>
</div>