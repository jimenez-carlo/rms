<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Accounting Report</div>
      </div>
      <div class="block-content collapse in">
        <?php echo form_open("", ["class" => "form-inline offset3"]); ?>
          <div class="row">
            <div class="span4">
              <label>
                Payment Type:
                <?php echo form_radio("payment_method", "CASH", true, ['id' =>'cash']); ?>
                <label class="radio inline" for="cash">CASH</label>
                <?php echo form_radio("payment_method", "EPP", false, ['id' =>'epp']);; ?>
                <label class="radio inline" for="epp">EPP</label>
              </label>
            </div>
          </div>
          <div class="row">
            <div class="control-group span3">
              <?php echo form_label("Deposit Date From:", "date-from", ["for" => "date-from", "class" => "control-label"]); ?>
              <div class="controls">
                <?php echo form_input("date_from", "", ["id" => "date-from", "class" => "datepicker", "placeholder" => "yyyy-mm-dd"]); ?>
              </div>
            </div>
            <div class="control-group span3">
              <?php echo form_label("Date To:", "date-to", ["for" => "date-to", "class" => "control-label"]); ?>
              <div class="controls">
                <?php echo form_input("date_to", "", ["id" => "date-to", "class" => "datepicker", "placeholder" => "yyyy-mm-dd"]); ?>
              </div>
            </div>
            <div class="control-group">
              <?php echo form_label("generate", "", ["class" => "control-label hidden"]); ?>
              <div class="controls">
                <?php echo form_button([ "name" => "generate", "value" => "true", "content" => "Generate", "type" => "submit", "class" => "btn btn-danger"]); ?>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

