<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Fund Transfer</div>
      </div>
      <div class="block-content collapse in">

        <form class="form-horizontal" method="post">
          <table class="table">
            <thead>
              <tr>
                <th><p>Region</p></th>
                <th><p>Company</p></th>
                <th><p>Brand New Registration Cost (Cash)</p></th>
                <th><p>Brand New Registration Cost (Installment)</p></th>
                <th><p>Brand New Target (Cash)</p></th>
                <th><p>Brand New Target (Installment)</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $row)
              {
                $key = '['.$row->rid.']';
                print '<tr>';
                print '<td>'.$row->region->name.'</td>';
                print '<td>'.$row->company->name.'</td>';
                print '<td>'.form_input('regn_bnew_cash'.$key, set_value('regn_bnew_cash'.$key, $row->regn_bnew_cash)).'</td>';
                print '<td>'.form_input('regn_bnew_inst'.$key, set_value('regn_bnew_cash'.$key, $row->regn_bnew_inst)).'</td>';
                print '<td>'.form_input('target_bnew_cash'.$key, set_value('regn_bnew_cash'.$key, $row->target_bnew_cash)).'</td>';
                print '<td>'.form_input('target_bnew_inst'.$key, set_value('regn_bnew_cash'.$key, $row->target_bnew_inst)).'</td>';
              }
              ?>
            </tbody>
          </table>

          <div class="form-actions">
            <input type="submit" name="submit" value="Save" class="btn btn-success">
          </div>
        </form>

			</div>
		</div>
  </div>
</div>