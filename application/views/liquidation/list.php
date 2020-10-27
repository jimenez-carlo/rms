<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Liquidation</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <fieldset>
            <div class="control-group span5">
              <div class="control-label">Date Deposited</div>
              <div class="controls">
                <span style="display:inline-block;width:50px">From:</span>
                <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-3 days'))), array('class' => 'datepicker', 'autocomplete' => 'off')); ?>
                <br>
                <span style="display:inline-block;width:50px">To:</span>
                <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker', 'autocomplete' => 'off')); ?>
              </div>
            </div>

            <?php
            if ($_SESSION['position'] != 108)
            {
              $options = array('_none' => '- Any -') + $region;
              print '<div class="control-group span4">';
              print '<div class="control-label">Region</div>';
              print '<div class="controls">';
              print form_dropdown('region', $options, set_value('region'));
              print '</div></div>';
            }
            ?>

            <div class="form-actions span12">
              <input type="submit" value="Search" class="btn btn-success" name="search">
            </div>
          </fieldset>
        </form>

        <hr>
        <table id="table_liq" class="table">
          <thead>
            <tr>
              <th><p>Reference #</p></th>
              <th><p>Document #</p></th>
              <!-- <th><p>Debit Memo #</p></th> -->
              <th><p>Date Deposited</p></th>
		<th><p>Company</p></th>
              <th><p>Region</p></th>
              <th><p># of Units</p></th>
              <th><p style="text-align:right">CA Amount</p></th>
              <th colspan="2"><p style="text-align:center">Liquidated Amount</p></th>
              <th colspan="2"><p style="text-align:center">For Liquidation</p></th>
              <th><p style="text-align:right">LTO Pending</p></th>
              <th><p style="text-align:right">Pending Amount</p></th>
            </tr>
          </thead>
          <tbody>
          <?php
          foreach ($table as $row)
          {
            $balance = $row->amount - ($row->liquidated + $row->misc_liquidated + $row->return_liquidated + $row->for_liquidation + $row->misc_for_liq  + $row->return_for_liq + $row->lto_pending);
            print '<tr>';
            print '<td><a data-vid="'.$row->vid.'" class="vid">'.$row->reference.'</a></td>';
            print '<td>'.$row->voucher_no.'</td>';
            // print '<td>'.$row->dm_no.'</td>';
            print '<td>'.substr($row->transfer_date, 0,10).'</td>';
            print '<td>'.$row->companyname.'</td>';
            print '<td>'.$region[$row->region].'</td>';
            print '<td>'.$row->sales_count.'</td>';
            print '<td style="text-align:right">'.number_format($row->amount,2,'.',',').'</td>';

            print '<td>Registration: <br>Miscellaneous: <br>Return Fund:</td>';
            print '<td style="text-align:right">
              '.number_format($row->liquidated,2,'.',',').'<br>
              '.number_format($row->misc_liquidated,2,'.',',').'<br>
              '.number_format($row->return_liquidated,2,'.',',').'
              </td>';
            print '<td>Registration: <br>Miscellaneous: <br>Return Fund:</td>';
            print '<td style="text-align:right">
              '.number_format($row->for_liquidation,2,'.',',').'<br>
              '.number_format($row->misc_for_liq,2,'.',',').'<br>
              '.number_format($row->return_for_liq,2,'.',',').'
            </td>';

            print '<td style="text-align:right">'.number_format($row->lto_pending,2,'.',',').'</td>';
            print '<td style="text-align:right">'.number_format($balance,2,'.',',').'</td>';
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

<form id="form_liq" method="post" action="<?= base_url() ?>liquidation/sales" target="_blank">
  <input type="hidden" name="vid" value="0" class="vid">
</form>

<script type="text/javascript">
  $(function(){
    $(document).ready(function(){
      $('#table_liq').on('click', 'tbody tr .vid', function(){
        var vid = $(this).attr('data-vid');
        if (vid) {
          $('#form_liq input.vid').val(vid);
          $('#form_liq').submit();
        }
      });
    });
  });
</script>
