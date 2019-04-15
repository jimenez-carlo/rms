<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Fund Audit</div>
      </div> 
      <div class="block-content collapse in">
        <form method="post" style="margin:0px;">
        <table class="table" style="margin:0px;">
          <thead>
            <tr>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th><p>Cash in Bank</p></th>
              <th><p>Cash on Hand</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              $key = '['.$row->fid.']';
              print '<tr>';
              print '<td>'.$row->region.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td>'.$row->fund.'</td>';
              print '<td>'.$row->cash_on_hand.'</td>';
              print '<td><a href="passbook/'.$row->region.'">Audit</a></td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr><td colspan=20>No result.</td></tr>';
            }
            ?>
          </tbody>
        </table>
        </form>
			</div>
		</div>
  </div>
</div>
