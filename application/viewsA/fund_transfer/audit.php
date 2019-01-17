<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Fund Transfer Audit</div>
      </div>
      <div class="block-content collapse in">
      <form class="form-horizontal" method="post" style="margin:10px 0px!important;">
        <fieldset style="height:30px;">
          <div class="control-group span4" style="margin-bottom:0;">
            <div class="control-label">Region</div>
            <div class="controls">
              <?php
              $options = array(
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
              print form_dropdown('region', $options, set_value('region'));
              ?>
            </div>
          </div>
          
          <div class="control-group span4" style="margin-bottom:0;">
            <div class="control-label">Company</div>
            <div class="controls">
              <?php
              $options = array(
                1 => 'MNC',
                3 => 'HPTI',
                2 => 'MTI',
              );
              print form_dropdown('company', $options, set_value('company'));
              ?>
            </div>
          </div>
          <div class="control-group span4" style="margin-bottom:0;">
            <input type="submit" name="search" value="Search" class="btn btn-success">
          </div>
        </fieldset>

        <?php if (isset($table)) {?>
        <br>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th><p>Date Transferred</p></th>
              <th><p>Debit Memo #</p></th>
              <th><p>Total Amount</p></th>
              <th><p>Transmittal Date</p></th>
              <th><p>Amount Breakdown</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $fund_transfer)
            {
              print '<tr>';
              print '<td>'.$fund_transfer->date.'</td>';
              print '<td>'.$fund_transfer->dm_no.'</td>';
              print '<td style="text-align:right;">&#x20b1 '.number_format($fund_transfer->amount,2,'.',',').'</td>';

              $ctr = 0;
              foreach ($fund_transfer->projected as $row)
              {
                if ($ctr > 0) print '<td></td><td></td><td></td>';

                print '<td>'.$row->date.'</td>';
                print '<td style="text-align:right;">'.number_format($row->amount,2,'.',',').'</td>';
                
                print '<td>';
                if ($ctr == 0) print '<a class="btn btn-success" href="fund_transfer/sprint/'.$fund_transfer->ftid.'">Print</a>';
                print '</td>';

                print '</tr>';
                $ctr++;
              }
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
        <?php } ?>

      </form>
      </div>
    </div>
  </div>
</div>