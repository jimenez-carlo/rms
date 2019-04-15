<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">NRU</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0">
          <table class="table">
            <thead>
              <tr>
                <th><p>Company</p></th>
                <th><p>Current Cash Fund</p></th>
                <th><p>Current Check Fund</p></th>
                <th><p>Total # of records for NRU</p></th>
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $fund)
              {
                $disabled = ($fund->sales == 0) ? 'disabled' : '';

                print "<tr>";
                print "<td>".$fund->company_name."</td>";
                print "<td>".number_format($fund->cash_on_hand, 2, '.', ',')."</td>";
                print "<td>".number_format($fund->cash_on_check, 2, '.', ',')."</td>";
                print "<td>".$fund->sales."</td>";

                print '<td><input type="submit" name="company['.$fund->company.']" value="Update" class="btn btn-success" '.$disabled.'></td>';
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