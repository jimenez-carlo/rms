<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php
print form_hidden('sid', set_value('sid', $sales->sid));
?>
<span class="cash hide"><?php echo $sales->fund; ?></span>
<span class="registration hide"><?php echo $sales->registration; ?></span>
<span class="tip hide"><?php echo $sales->tip; ?></span>

<div class="control-group">
  <div class="control-label">Cash on Hand</div>
  <div class="controls cash-on-hand"><?php print $sales->fund; ?></div>
</div>
<div class="control-group">
  <div class="control-label">Engine #</div>
  <div class="controls"><?php print $sales->engine_no; ?></div>
</div>
<div class="control-group">
  <div class="control-label">Branch</div>
  <div class="controls"><?php print $sales->branch->b_code.' '.$sales->branch->name; ?></div>
</div>
<div class="control-group">
  <div class="control-label">Customer Name</div>
  <div class="controls"><?php print $sales->first_name.' '.$sales->last_name; ?></div>
</div>
<div class="control-group">
  <div class="control-label">Type of Sales</div>
  <div class="controls"><?php print $sales->sales_type; ?></div>
</div>

<div class="control-group">
  <?php
    echo form_label('<text style="color:red;">*</text> Registration', 'registration', array('class' => 'control-label'));
    echo '<div class="controls">';
    echo form_input('registration', set_value('registration', $sales->registration), array('class' => 'numeric'));
    echo '</div>';
  ?>
</div>
<div class="control-group">
  <?php
    echo form_label('<text style="color:red;">*</text> Tip', 'tip', array('class' => 'control-label'));
    echo '<div class="controls">';
    echo form_input('tip', set_value('tip', $sales->tip), array('class' => 'numeric'));
    echo '</div>';
  ?>
</div>
<div class="control-group">
  <?php
    echo form_label('<text style="color:red;">*</text> CR #', 'cr_no', array('class' => 'control-label'));
    echo '<div class="controls">';
    echo form_input('cr_no', set_value('cr_no', $sales->cr_no));
    echo '</div>';
  ?>
</div>
<div class="control-group">
  <?php
    echo form_label('<text style="color:red;">*</text> MVF #', 'mvf_no', array('class' => 'control-label'));
    echo '<div class="controls">';
    echo form_input('mvf_no', set_value('mvf_no', $sales->mvf_no));
    echo '</div>';
  ?>
</div>
<div class="control-group">
  <?php
    echo form_label('Plate #', 'plate_no', array('class' => 'control-label'));
    echo '<div class="controls">';
    echo form_input('plate_no', set_value('plate_no', $sales->plate_no));
    echo '</div>';
  ?>
</div>
<div class="form-actions">
  <input type="submit" class="btn btn-success" id="save" value="Save" name="submit">
  <a class="btn btn-success calculate hide">Calculate</a>
</div>