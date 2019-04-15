<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left"><?php print $page_title; ?></div>
      </div>
      <div class="block-content collapse in">

        <!-- List Form -->
        <form class="form-horizontal" method="post" style="margin:0;">
          <table class="table" style="margin:0;">
            <thead>
              <tr>
                <th><p>Transaction #</p></th>
                <th><p>Date</p></th>
                <th><p>Region</p></th>
                <th><p>Company</p></th>
                <th><p>Status</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $row)
              {
                if ($row->alert > 0) print '<tr class="info">';
                else print '<tr>';
                
                print '<td><a href="orcr_checking/view/'.$row->tid.'">'.$row->trans_no.'</a></td>';
                print '<td>'.$row->date.'</td>';
                print '<td>'.$row->region.'</td>';
                print '<td>'.$row->company.'</td>';
                print '<td>'.$row->status.'</td>';
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
                  </tr>';
              }
              ?>
            </tbody>
          </table>
        </form>

			</div>
		</div>
  </div>
</div>