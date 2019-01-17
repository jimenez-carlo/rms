<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">LTO Pending</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0">
          <table class="table" style="margin:0">
            <thead>
              <tr>
                <th><p>Transmittal Date</p></th>
                <th><p>Branch</p></th>
                <th><p>Customer Type</p></th>
                <th><p>Status</p></th>
                <th><p>Items</p></th>
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $row)
              {
                print "<tr>";
                print "<td>".$row->date."</td>";
                print "<td>".$row->branch->b_code." ".$row->branch->name."</td>";
                print "<td>".$row->cust_type."</td>";
                print "<td>".$row->status."</td>";
                print "<td>".$row->sales_count."</td>";
                print "<td><a href='lto_pending/view/".$row->ltid."'' class='btn btn-success'>Update</a></td>";
                print "</tr>";
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
