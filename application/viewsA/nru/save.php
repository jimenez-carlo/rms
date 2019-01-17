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

<form method="post" class="form-horizontal" style="margin: 0px;" onkeypress="return event.keyCode != 13;" onsubmit="return confirm('Items with Registration Amount will proceed to the next process. Continue?');>">
<?php
print form_hidden('cash[1]', $cash[1]);
print form_hidden('cash[2]', $cash[2]);
print form_hidden('cash[3]', $cash[3]);
print form_hidden('check[1]', $check[1]);
print form_hidden('check[2]', $check[2]);
print form_hidden('check[3]', $check[3]);

foreach ($registration as $key => $value)
{
  if ($value > 0)
  {
    print form_hidden('registration['.$key.']', $value);
    print form_hidden('fund['.$key.']', $value);
  }
}

?>

<div class="container-fluid">
  <div class="row-fluid">

    <!-- block -->
    <div class="block span12 fund">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Fund</div>
      </div>
      <div class="block-content collapse in span">
        <div class="span3">
          <div class="control-group">
            <label style="float:left">MNC Cash Fund</label>
            <div style="float:right"><?php print number_format($cash_on_hand[1], 2, '.', ',')  ; ?></div>
          </div>
          <div class="control-group">
            <label style="float:left">Total Expense</label>
            <div style="float:right; <?php if($cash_on_hand[1] < $cash[1]) print 'color:red'; ?>" class="mnc-exp-cash"><?php print number_format($cash[1], 2, '.', ','); ?></div>
          </div>
          <hr>
          <div class="control-group">
            <label style="float:left">MTI Cash Fund</label>
            <div style="float:right"><?php print number_format($cash_on_hand[2], 2, '.', ',')  ; ?></div>
          </div>
          <div class="control-group">
            <label style="float:left">Total Expense</label>
            <div style="float:right; <?php if($cash_on_hand[2] < $cash[2]) print 'color:red'; ?>" class="mti-exp-cash"><?php print number_format($cash[2], 2, '.', ','); ?></div>
          </div>
          <hr>
          <div class="control-group">
            <label style="float:left">HPTI Cash Fund</label>
            <div style="float:right"><?php print number_format($cash_on_hand[3], 2, '.', ',')  ; ?></div>
          </div>
          <div class="control-group">
            <label style="float:left">Total Expense</label>
            <div style="float:right; <?php if($cash_on_hand[3] < $cash[3]) print 'color:red'; ?>" class="hpti-exp-cash"><?php print number_format($cash[3], 2, '.', ','); ?></div>
          </div>
        </div>
        <div class="span1"></div>
        <div class="span3">
          <div class="control-group">
            <label style="float:left">MNC Check Fund</label>
            <div style="float:right"><?php print number_format($cash_on_check[1], 2, '.', ',')  ; ?></div>
          </div>
          <div class="control-group">
            <label style="float:left">Total Expense</label>
            <div style="float:right; <?php if($cash_on_check[1] < $check[1]) print 'color:red'; ?>" class="mnc-exp-check"><?php print number_format($check[1], 2, '.', ','); ?></div>
          </div>
          <hr>
          <div class="control-group">
            <label style="float:left">MTI Check Fund</label>
            <div style="float:right"><?php print number_format($cash_on_check[2], 2, '.', ',')  ; ?></div>
          </div>
          <div class="control-group">
            <label style="float:left">Total Expense</label>
            <div style="float:right; <?php if($cash_on_check[2] < $check[2]) print 'color:red'; ?>" class="mti-exp-check"><?php print number_format($check[2], 2, '.', ','); ?></div>
          </div>
          <hr>
          <div class="control-group">
            <label style="float:left">HPTI Check Fund</label>
            <div style="float:right"><?php print number_format($cash_on_check[3], 2, '.', ',')  ; ?></div>
          </div>
          <div class="control-group">
            <label style="float:left">Total Expense</label>
            <div style="float:right; <?php if($cash_on_check[3] < $check[3]) print 'color:red'; ?>" class="hpti-exp-check"><?php print number_format($check[3], 2, '.', ','); ?></div>
          </div>
        </div>
        <div class="span5"></div>
        <div style="clear:both"></div>
        <div class="form-actions" style="padding-left:150px">
          <p style="font-weight:bold;">Items with Registration Amount will proceed to the next process. Continue?</p>
          <input type="submit" class="btn btn-success" value="Yes" name="submit" <?php print $submit_btn; ?>>
          <a class="btn btn-success" href="nru">Cancel</a>
        </div>
      </div>
    </div>

  </div>
</div>

</form>