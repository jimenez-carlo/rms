<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">CA List</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <fieldset>
            <div class="control-group span5">
              <div class="control-label">Entry Date</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d')), array('class' => 'datepicker')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker')); ?>
              </div>
            </div>

            <?php
              $status = array('_any' => '- Any -') + $status;
              echo '<div class="control-group span5">';
              echo form_label('Status', 'status', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_dropdown('status', $status, set_value('status', $def_stat));
              echo '</div></div>';

<<<<<<< HEAD
              $regions = array(
                '_any' => '- Any -',
                1 => 'NCR',
                2 => 'Region 1',
                3 => 'Region 2',
                4 => 'Region 3',
                5 => 'Region 4A',
                6 => 'Region 4B',
                7 => 'Region 5',
                8 => 'Region 6',
                9 => 'Region 7',
                10 => 'Region 8',
              );
=======
              $luzon_visayas = array(
                '_any' => '- Any -',
                   1   => 'NCR',
                   2   => 'Region 1',
                   3   => 'Region 2',
                   4   => 'Region 3',
                   5   => 'Region 4A',
                   6   => 'Region 4B',
                   7   => 'Region 5',
                   8   => 'Region 6',
                   9   => 'Region 7',
                   10  => 'Region 8'
              );

              $mindanao = array(
                '_any' => '- Any -',
                 11    => 'IX',
                 12    => 'X',
                 13    => 'XI',
                 14    => 'XII',
                 15    => 'XIII'
              );

              $regions = ($_SESSION['company'] != 8) ? $luzon_visayas : $mindanao;

>>>>>>> production.50
              echo '<div class="control-group span5">';
              echo form_label('Region', 'region', array('class' => 'control-label'));
              echo '<div class="controls">';
              echo form_dropdown('region', $regions, set_value('region'));
              echo '</div></div>';
            ?>
            <div class="form-actions span4">
              <input type="submit" class="btn btn-success" value="Apply">
            </div>
          </fieldset>
        </form>

        <hr>
        <table class="table">
          <thead>
            <tr>
              <th><p>Reference #</p></th>
              <th><p>Document #</p></th>
              <th><p>Entry Date</p></th>
              <th><p>Debit Memo #</p></th>
              <th><p>Date Deposited</p></th>
              <th style="text-align:right;padding-right:10px;"><p>Amount</p></th>
              <th><p>Region</p></th>
              <!-- <th><p>Company</p></th> -->
              <th><p>Status</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              print '<tr>';
              print '<td>'.$row->reference.'</td>';
              print '<td>'.$row->voucher_no.'</td>';
              print '<td>'.$row->date.'</td>';
              print '<td>'.$row->dm_no.'</td>';
              print '<td>'.$row->transfer_date.'</td>';
              print '<td style="text-align:right;padding-right:10px;">'.number_format($row->amount,2,'.',',').'</td>';
              print '<td>'.$row->region.'</td>';
              // print '<td>'.$row->company.'</td>';
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
                <td></td>
                <td></td>
                <td></td>
                </tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>