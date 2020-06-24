<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Create Topsheet</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal tbl-form" method="post">
          <table class="table">
            <thead>
              <tr>
              	<th><p>Transaction #</p></th>
              	<th><p><?php print $trans_no; ?></p></th>
              	<th><p></p></th>
              	<th><p></p></th>
              	<th><p>Date</p></th>
              	<th><p><?php print date('Y-m-d'); ?></p></th>
              </tr>
              <tr>
                <th><p>Region</p></th>
                <th><p>Branch Code</p></th>
                <th><p>Branch Name</p></th>
                <th><p>RERFO Date</p></th>
                <th><p>Reference</p></th>
                <th><p style="text-align: right">Amount</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $bcode = '0';
              $tot_amt = $tot_exp = $sub_total = 0;
              foreach ($table as $row)
              {
                print form_hidden('rid[]', $row->rid);

              	// first row
              	if ($bcode == '0') $bcode = substr($row->bcode, 0, 1);
                if ($bcode != substr($row->bcode, 0, 1))
                {
                  print '<tr>
                    <th>Total SUM</th>
                    <th>SUB TOTAL</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th style="text-align: right">'.number_format($sub_total, 2, '.', '').'</th>
                    </tr>';
                  $sub_total = 0;
                  $bcode = substr($row->bcode, 0, 1);
                }

                print '<tr>';
                print '<td>'.$regions[$row->region].'</td>';
                print '<td>'.$row->bcode.'</td>';
                print '<td>'.$row->bname.'</td>';
                print '<td>'.$row->date.'</td>';
                print '<td><a href="'.base_url().'rerfo/view/'.$row->rid.'" target="_blank">'.$row->trans_no.'</a></td>';
                print '<td style="text-align: right">'.number_format($row->registration, 2, '.', '').'</td>';
                print '</tr>';

                $tot_amt += $row->amount;
                $tot_exp += $row->registration;
                $sub_total += $row->registration;
              }

              // last row
              if ($bcode != '0')
              {
                print '<tr>
                  <th>Total SUM</th>
                  <th>SUB TOTAL</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th style="text-align: right">'.number_format($sub_total, 2, '.', '').'</th>
                  </tr>';
                $sub_total = 0;
                $bcode = '0';
              }

              if (empty($table))
              {
                print '<tr>
                  <td style="color: red; font-weight: bold;">No RERFO for Topsheet.</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  </tr>';
              }
              ?>
            </tbody>
            <tfoot>
            	<tr style="color: red">
            		<th>TOTAL</th>
            		<th></th>
            		<th></th>
            		<th></th>
                <th></th>
                <th style="text-align: right"><?php print number_format($tot_exp, 2, '.', ''); ?></th>
            	</tr>
            </tfoot>
          </table>

          <div class="form-actions">
            <?php
            print form_hidden('tot_amt', $tot_amt);
            print form_hidden('tot_exp', $tot_exp);
            if (count($table) !== 0) {
              print form_submit('submit', 'Continue', array('class' => 'btn btn-success'));
            }
            ?>
          </div>
        </form>
			</div>
		</div>
  </div>
</div>
