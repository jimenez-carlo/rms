<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
    <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Transaction # <?php print $transmittal->trans_no; ?></div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post" onsubmit="return confirm('This action cannot be undone: Tag transmittal as received. Continue?')">
          <?php print form_hidden('tid', $transmittal->tid); ?>

          <table class="table">
            <thead>
              <th><p>Branch</p></th>
              <th><p>Customer Name</p></th>
              <th><p>CR #</p></th>
              <th><p>Status</p></th>
              <th><p></p></th>
            </thead>
            <tbody>
              <?php
              foreach ($transmittal->sales as $sales)
              {
                print '<tr>';
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                print '<td>'.$sales->cr_no.'</td>';

                if (empty($sales->received_date)) {
                  print '<td>'.$sales->status.'</td>';
                }
                else {
                  print '<td>'.$sales->status.' on '.$sales->received_date.'</td>';
                }

                // print (!empty($sales->last_user))
                //   ? '<td>by '.$sales->last_user->firstname.' '.$sales->last_user->lastname.'</td>'
                //   : '<td><i>No remarks.</i></td>';

                print '<td>';
                if ($_SESSION['position'] != 108 && empty($sales->received_date)) {
                  // $view = ($_SESSION['position'] == 108) ? 'Unhold' : 'View Remarks';
                  // print'<a class="btn btn-success" onclick="view('.$transmittal->tid.','.$sales->sid.')">'.$view.'</a> ';
                  print form_submit('receive['.$sales->sid.']', 'Tag as Received', array('class' => 'btn btn-success'));
                }
                print '</td>';
                print '</tr>';
              }
              ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>