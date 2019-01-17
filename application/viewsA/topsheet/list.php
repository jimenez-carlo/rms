<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Topsheet Status</div>
      </div>
      <div class="block-content collapse in">

        <!-- Search Form -->
        <form class="form-horizontal" method="post" style="margin:10px 0px;">
          <fieldset>
            <div class="row-fluid">
              <div class="control-group span4">
                <?php
                  echo form_label('Transaction #', 'trans_no', array('class' => 'control-label'));
                  echo '<div class="controls">';
                  echo form_input('trans_no', set_value('trans_no'), array('style' => 'width:100%'));
                  echo '</div>';
                ?>
              </div>
            <div class="span4">
              <input type="submit" class="btn btn-success" value="Search" name="search">
            </div>
            </div>
          </fieldset>
        </form>

        <!-- List Form -->
        <form class="form-horizontal" method="post" style="margin:10px 0px;">
          <table class="table">
            <thead>
              <tr>
                <th><p>Transaction #</p></th>
                <th><p>Company</p></th>
                <th><p>Registration Date</p></th>
                <th><p>Topsheet Amount</p></th>
                <th><p>Status</p></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($table as $topsheet)
              {
                print '<tr>';
                print '<td><a href="ts_view/'.$topsheet->tid.'">'.$topsheet->trans_no.'</a></td>';
                print '<td>'.$topsheet->company.'</td>';
                print '<td>'.$topsheet->date.'</td>';
                print '<td>'.$topsheet->total_expense.'</td>';
                print '<td>'.$topsheet->status.'</td>';

                print '</tr>';
              }

              if (empty($topsheet))
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