<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style type="text/css">
.table {
  font-size: 10pt;
}
input[type="text"] {
  height: 18px;
}
.control-group {
  margin-bottom: 2px !important;
}
hr {
  margin:10px !important;
}
</style>

<form method="post" class="form-horizontal" style="margin: 0px;" onkeypress="return event.keyCode != 13;" onsubmit="return confirm('Items with Registration Amount will proceed to the next process. Continue?');>
<?php
print form_hidden('amount[1]', '0.00');
print form_hidden('amount[2]', '0.00');
print form_hidden('amount[3]', '0.00');
?>

<div class="container-fluid">
  <div class="row-fluid">

    <!-- block -->
    <div class="block span9">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">NRU</div>
      </div>
      <div class="block-content collapse in">
        <table class="table">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th class="pull-right"><a class="btn btn-success">APPLY TO ALL</a></th>
              <th><?php print form_input('registration_all', set_value('registration_all', '0.00'), array('class' => 'registration_all numeric', 'style' => 'width:75px')); ?></th>
            </tr>
            <tr>
              <th><p>Branch</p></th>
              <th><p>Pending Date</p></th>
              <th><p>Date Sold</p></th>
              <th><p>Engine #</p></th>
              <th><p>Customer Name</p></th>
              <th><p>Registration</p></th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if (!empty($table))
            {
              foreach($table as $sales)
              {
                $key = "[".$sales->sid."]";

                if (substr($sales->branch->b_code, 0, 1) == '1') $company = 'mnc';
                if (substr($sales->branch->b_code, 0, 1) == '6') $company = 'mti';
                if (substr($sales->branch->b_code, 0, 1) == '3') $company = 'hpti';

                print '<tr>';
                print '<td>'.$sales->branch->b_code.' '.$sales->branch->name.'</td>';
                print '<td>'.$sales->pending_date.'</td>';
                print '<td>'.substr($sales->date_sold, 0, 10).'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';
                print '<td>'.form_input('registration'.$key, 
                  set_value('registration'.$key, $sales->registration), 
                  array('class' => 'registration numeric '.$company,
                    'style' => 'width:75px')).'</td>';
                print '</tr>';
              }
            }
            else
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
      </div>
    </div>

    <!-- block -->
    <div class="block span3 fund">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Fund</div>
      </div>
      <div class="block-content collapse in">
        <div class="control-group">
          <label style="float:left">MNC Fund</label>
          <div style="float:right"><?php print number_format($cash_on_check[1], 2, '.', ',')  ; ?></div>
        </div>
        <div class="control-group">
          <label style="float:left">Total Expense</label>
          <div style="float:right" class="mnc-exp">0.00</div>
        </div>
        <hr>
        <div class="control-group">
          <label style="float:left">MTI Fund</label>
          <div style="float:right"><?php print number_format($cash_on_check[2], 2, '.', ',')  ; ?></div>
        </div>
        <div class="control-group">
          <label style="float:left">Total Expense</label>
          <div style="float:right" class="mti-exp">0.00</div>
        </div>
        <hr>
        <div class="control-group">
          <label style="float:left">HPTI Fund</label>
          <div style="float:right"><?php print number_format($cash_on_check[3], 2, '.', ',')  ; ?></div>
        </div>
        <div class="control-group">
          <label style="float:left">Total Expense</label>
          <div style="float:right" class="hpti-exp">0.00</div>
        </div>
        <div class="form-actions" style="padding-left:150px">
          <a class="btn btn-success calculate hide">Calculate</a>
          <input type="submit" class="btn btn-success" value="Save" name="submit">
        </div>
      </div>
    </div>

  </div>
</div>

</form>