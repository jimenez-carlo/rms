<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Please select date of transmittal</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0">
          <?php print form_hidden('company', $company); ?>

          <table class="table">
            <thead>
              <tr>
                <th><p><input type="submit" name="back[1]" value="Back" class="btn btn-success"></p></th>
                <th><p>Pending date since</p></th>
                <th><p>Included transmittal dates</p></th>
                <th><p>Total # of records for NRU</p></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($table as $row)
              {
                print "<tr>";
                print '<td><input type="radio" name="date" value="'.$row->pending_date.'"></td>';
                print "<td>".$row->pending_date."</td>";
                print "<td>".str_replace(',', '<br>', $row->transmittal_date)."</td>";
                print "<td>".$row->sales."</td>";
                print "</tr>";
              }

              if (empty($table))
              {
                print '<tr>
                  <td>No result.</td>
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
    $('input').click(function(){
      $('form').submit();
    });
  });
});
</script>
