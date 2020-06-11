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
        <form class="form-horizontal" method="post" action="view">
          <table class="table">
            <thead>
              <th><p>Rerfo #</p></th>
              <th><p># of Units</p></th>
              <th><p></p></th>
            </thead>
            <tbody>
              <?php
              foreach ($table as $row)
              {
                print '<tr>';
                print '<td>'.$row->trans_no.'</td>';
                print '<td>'.$row->sales_count.'</td>';
                print '<td>'.form_submit('view_tr['.$row->rid.']', 'View Transmittal', array('class' => 'btn btn-success')).'</td>';
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
        </form>
      </div>
    </div>
  </div>
</div>
