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

        <form class="form-horizontal" method="post" style="margin:10px 0px">
          <fieldset>
            <div class="control-group span5">
              <div class="control-label">Topsheet Date</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-3 days'))), array('class' => 'datepicker')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker')); ?>
              </div>
            </div>

            <?php
              // $company = array(
              //     '_any' => '- Any -',
              //     1 => 'MNC',
              //     3 => 'HPTI',
              //     2 => 'MTI',
              //   );
              // echo '<div class="control-group span5">';
              // echo form_label('Company', 'company', array('class' => 'control-label'));
              // echo '<div class="controls">';
              // echo form_dropdown('company', $company, set_value('company'));
              // echo '</div></div>';

              // $pre_status = array(
              //     '_any' => '- Any -',
              //     '_t' => 'For Transmittal',
              //   );
              // $status = array_merge($pre_status, $status);
              // echo '<div class="control-group span5">';
              // echo form_label('Status', 'status', array('class' => 'control-label'));
              // echo '<div class="controls">';
              // echo form_dropdown('status', $status, set_value('status', 0));
              // echo '</div></div>';
            ?>

            <div class="form-actions span12">
              <input type="submit" name="search" value="Search" class="btn btn-success">
            </div>
          </fieldset>
        </form>

        <hr>
        <form class="form-horizontal tbl-form" method="post" target="_blank">
          <?php print form_hidden('tid', 0); ?>

          <table class="table">
            <thead>
              <tr>
                <th><p>Transaction #</p></th>
                <!-- <th><p>Company</p></th> -->
                <th><p>Topsheet Date</p></th>
                <!-- <th><p>Status</p></th> -->
                <!-- <th><p>Log</p></th> -->
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($table as $topsheet)
              {
                $log = '';

                if (!empty($topsheet->print_date)) {
                  $review = '<input type="submit" name="view_ts['.$topsheet->tid.']" value="View Topsheet" class="btn btn-success btn-view-ts">';
                  $log .= 'Printed on '.$topsheet->print_date.'<br>';
                }
                else {
                  $review = '<input type="submit" name="review['.$topsheet->tid.']" value="Review" class="btn btn-success btn-review">';
                  $log .= '<i>Pending review</i><br>';
                }

                if (!empty($topsheet->transmittal_date)) {
                  $transmit = '<input type="submit" name="view_tr['.$topsheet->tid.']" value="View Transmittal" class="btn btn-success btn-view-tr">';
                  $log .= 'Transmitted on '.$topsheet->transmittal_date.'<br>';
                }
                else {
                  $transmit = '<input type="submit" name="transmit['.$topsheet->tid.']" value="Transmit" class="btn btn-success btn-transmit">';
                  $log .= '<i>Pending transmittal</i><br>';
                }

                print '<tr>';
                print '<td>'.$topsheet->trans_no.'</td>';
                // print '<td>'.$topsheet->company.'</td>';
                print '<td>'.$topsheet->date.'</td>';
                // print '<td>'.$topsheet->status.'</td>';
                // print '<td>'.$log.'</td>';
                // print '<td>'.$review.' '.$transmit.'</td>';

                print '<td>';
                if ($_SESSION['position'] == 108) {
                  print form_submit('view', 'View', array('class' => 'btn btn-success', 'data-value' => $topsheet->tid)).' ';
                  print form_submit('print', 'Print', array('class' => 'btn btn-success', 'data-value' => $topsheet->tid)).' '; 
                }
                print form_submit('transmit', 'Transmittal', array('class' => 'btn btn-success', 'data-value' => $topsheet->tid)).' ';
                print '</td>';
                print '</tr>';
              }

              if (empty($topsheet))
              {
                print '<tr>
                  <td>No result.</td>
                  <td></td>
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

<script type="text/javascript">
$(function(){
  $(document).ready(function(){
    $(".table").dataTable({
      "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
      "sPaginationType": "bootstrap",
      "oLanguage": {
        "sLengthMenu": "_MENU_ records per page"
      },
      "bFilter": false,
      "bSort": false,
      "iDisplayLength": 5,
      "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    });

    $('.btn-review').click(function(){
      $('.tbl-form').attr('action', 'topsheet/review').removeAttr('target');
    });
    $('.btn-transmit').click(function(){
      $('.tbl-form').attr('action', 'topsheet/transmittal').attr('target', '_blank');
    });
    $('.btn-view-ts').click(function(){
      $('.tbl-form').attr('action', 'topsheet/view').removeAttr('target');
    });
    $('.btn-view-tr').click(function(){
      $('.tbl-form').attr('action', 'transmittal/view').removeAttr('target');
    });

    $('.tbl-form input[type=submit]').click(function(){
      var name = $(this).attr('name');
      var tid = $(this).attr('data-value');

      $('input[name=tid]').val(tid);

      switch (name) {
        case 'view':
          $('form').attr('action', 'topsheet/view');
          break;
        case 'print':
          $('form').attr('action', 'topsheet/sprint');
          break;
        case 'transmit':
          $('form').attr('action', 'topsheet/transmittal');
          break;
      }
    });
  });
});
</script>
