<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Rerfo</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post">
          <?php print form_hidden('rid', $rerfo->rid); ?>
          <?php print form_hidden('sid', 0); ?>

          <table class="table">
            <thead>
              <tr>
                <th colspan="7">
                  <p>DAILY RERFO</p>
                  <p>Branch: <?php print $rerfo->bcode.' '.$rerfo->bname; ?></p>
                  <p>Period Covered: <?php print $rerfo->date ?></p>
                </th>
                <th colspan="6">
                  <p>RERFO ID: <?php print $rerfo->trans_no; ?></p>
                  <p>Date: <?php print date('Y-m-d'); ?></p>
                </th>
              </tr>
              <tr>
                <th><p>Registration Type</p></th>
                <th><p>Reference AR #</p></th>
                <th><p>Motor Type</p></th>
                <th><p>Date Sold</p></th>
                <th><p>Cust Code</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Engine #</p></th>
                <th><p>Target</p></th>
                <th><p>Amount Given</p></th>
                <th><p>Insurance</p></th>
                <!-- <th><p>LTO Tip</p></th> -->
                <th><p>LTO Registration</p></th>
                <th><p>Balance</p></th>
                <!-- <th><p>CR #</p></th>
                <th><p>MV File #</p></th> -->
                <th><p></p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $enable_save = $tot_tgt = $tot_amt = $tot_ins = $tot_reg = $tot_tip = $tot_bal = 0;
              foreach ($rerfo->sales as $sales)
              {
                print '<tr>';
                print '<td>'.$sales->registration_type.'</td>';
                print '<td>'.$sales->ar_no.'</td>';
                print '<td>'.$sales->sales_type.'</td>';
                print '<td>'.$sales->date_sold.'</td>';
                print '<td>'.$sales->cust_code.'</td>';
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td style="text-align: right">1,500.00</td>';
                print '<td style="text-align: right">'.number_format($sales->amount, 2, '.', ',').'</td>';
                print '<td style="text-align: right">300.00</td>';
                // print '<td>'.number_format($sales->tip, 2, '.', ',').'</td>';
                print '<td style="text-align: right">'.number_format($sales->registration, 2, '.', ',').'</td>';

                $bal = 1500 - 300 - $sales->registration - $sales->tip;
                print '<td style="text-align: right">'.number_format($bal, 2, '.', ',').'</td>';
                // print '<td>'.$sales->cr_no.'</td>';
                // print '<td>'.$sales->mvf_no.'</td>';

                print '<td>';
                if ($_SESSION['position'] == '108')
                {
                  if ($sales->topsheet > 0) print '<p style="color: green">Validated</p>';
                  else {
                    $enable_save = 1;
                    print '<label style="white-space: nowrap">'.form_checkbox('check['.$sales->sid.']', $sales->sid, set_value('check['.$sales->sid.']', ($sales->topsheet == -1))).' Validated</label>';
                  }

                  if ($sales->registration_date > date('Y-m-d H:i:s', strtotime('-3 day'))) {
                    // print '<td><input type="submit" name="update['.$sales->sid.']" value="View" class="btn btn-success view"></td>';
                    print '<p><a class="btn btn-success" onclick="view('.$sales->sid.')">View</a></p>';
                    print '<p style="color:red">Editable till '.date('Y-m-d H:i', strtotime($sales->registration_date.' +3 day')).'</p>';
                  }
                  else {
                    // print '<td><input type="submit" name="update['.$sales->sid.']" value="View" class="btn btn-success view"></td>';
                    print '<p><a class="btn btn-success" href="'.base_url().'sales/view/'.$sales->sid.'" target="_blank">View</a></p>';
                  }
                }
                print '<td>';
                print '</tr>';

                $tot_tgt += 1500;
                $tot_amt += $sales->amount;
                $tot_ins += 300;
                $tot_reg += $sales->registration;
                $tot_tip += $sales->tip;
                $tot_bal += $bal;
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Total SUM</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="text-align: right">&#x20b1 <?php print number_format($tot_tgt, 2, ".", ","); ?></th>
                <th style="text-align: right">&#x20b1 <?php print number_format($tot_amt, 2, ".", ","); ?></th>
                <th style="text-align: right">&#x20b1 <?php print number_format($tot_ins, 2, ".", ","); ?></th>
                <!-- <th style="text-align: right;">&#x20b1 <?php print number_format($tot_tip, 2, ".", ","); ?></th> -->
                <th style="text-align: right">&#x20b1 <?php print number_format($tot_reg, 2, ".", ","); ?></th>
                <th style="text-align: right">&#x20b1 <?php print number_format($tot_bal, 2, ".", ","); ?></th>
                <th></th>
              </tr>
            </tfoot>
          </table>

          <fieldset>
            <div class="form-actions span12" style="margin:0px;">
              <?php
              $key = '['.$rerfo->rid.']';

              if ($enable_save) {
                print '<input type="submit" name="save" value="Save changes" class="btn btn-success submit"> ';
              }

              $print_date = (!empty($print_date)) ? substr($rerfo->print_date, 0, 10) : date('Y-m-d');
              if ($rerfo->print == 0 || $print_date == date('Y-m-d')) {
                print '<input type="submit" name="print'.$key.'" value="Print" class="btn btn-success print"> ';
              }
              else {
                print '<input type="submit" name="request'.$key.'" value="Request Reprinting" class="btn btn-success request"> ';
              }
              ?>
            </div>
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
function view(sid) {
  $('input[name=sid]').val(sid);
  $('form').attr('action', '/registration');
  $('form').attr('target', '_blank');
  $('form').submit();
}

$(function(){
  $(document).ready(function(){
    $('.submit').click(function(){
      $('form').removeAttr('action');
      $('form').removeAttr('target');
      return confirm('Please make sure all information are correct before proceeding. Continue?');
    });
    $('.print').click(function(){
      $('form').attr('action', 'sprint');
      $('form').attr('target', '_blank');
    });
    $('.request').click(function(){
      $('form').attr('action', 'request');
      $('form').removeAttr('target');
      return confirm('The following action cannot be undone: Request reprinting of rerfo. Continue?');
    });
  });
});
</script>
