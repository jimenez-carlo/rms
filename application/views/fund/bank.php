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
              <th><p>Maintaining Balance</p></th>
              <th><p>Account #</p></th>
              <th><p>Signatories</p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($table as $row)
            {
              $key = '['.$row->fid.']';
              print '<tr>';
              print '<td>'.$row->region_name.'</td>';
              print '<td>'.$row->company_name.'</td>';
              print '<td>'.form_input('bank'.$key, set_value('bank', $row->bank)).'</td>';
              print '<td>'.form_input('m_balance'.$key, set_value('m_balance', $row->m_balance)).'</td>';
              print '<td>'.form_input('acct_number'.$key, set_value('acct_number', $row->acct_number)).'</td>';
              print '<td>';
              print '1. '.form_input('sign_1'.$key, set_value('sign_1', $row->sign_1)).'<br>';
              print '2. '.form_input('sign_2'.$key, set_value('sign_2', $row->sign_2)).'<br>';
              print '3. '.form_input('sign_3'.$key, set_value('sign_3', $row->sign_3));
              print '</td>';
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
