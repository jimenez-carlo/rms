<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//print_r($table);
?>

<div class="container-fluid">
	<div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Fund Audit</div>
      </div>
      <div class="block-content collapse in">
        <form method="post">
        <table class="table">
          <thead>
            <tr>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th><p>Bank</p></th>
              <th><p>Cash in Bank</p></th>
              <th><p>Cash on Hand</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              $key = '['.$row->fid.']';
              print '<tr>';
              print '<td>'.$row->region.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td>'.form_input('bank'.$key, set_value('bank', $row->bank)).'</td>';
              print '<td>'.$row->fund.'</td>';
              print '<td>'.$row->cash_on_hand.'</td>';
              print '</tr>';
            }

            if (empty($table))
            {
              print '<tr><td colspan=20>No result.</td></tr>';
            }
            ?>
          </tbody>
        </table>

        <div class="form-actions">
          <input type="submit" value="Save" class="btn btn-success">
        </div>
        </form>
			</div>
		</div>
  </div>
</div>
