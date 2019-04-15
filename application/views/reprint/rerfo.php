<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Approve Rerfo Request</div>
      </div>
      <div class="block-content collapse in">

        <form method="post" style="margin:0px;">
          <table class="table" style="margin:0px;">
            <thead>
              <tr>
                <th><p>Transaction #</p></th>
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $row)
              {
                $key = '['.$row->rid.']';
                print '<tr>';
                print '<td>'.$row->trans_no.'</td>';
                print '<td><input type="submit" value="Approve" name="approve'.$key.'" class="btn btn-success"></td>';
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
