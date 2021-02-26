<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="block">
  <div class="navbar navbar-inner block-header">
    <div class="pull-left">Penalty Request for Issuance of Check</div>
    <?php $disable = (form_error()) ? true : false; ?>
    <?php echo form_button([
      "id"=>"modal-btn", "class"=>"btn btn-success pull-right", "type"=>"button",
      "data-toggle"=>"modal", "data-target"=>"#ric-number-modal", "disabled"=>$disable,
      "content"=>"Input RIC Number"
    ]); ?>
  </div>
</div>
<?php echo form_open(base_url()."ric/penalty", ["id"=>"ric-form"]); ?>
  <table class="table">
    <thead>
      <tr>
        <th></th>
        <th>E-Payment Reference</th>
        <th>Number of MC</th>
        <th>Total Amount Penalty</th>
        <th>Company</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($batches)): ?>
        <?php foreach($batches AS $key => $batch): ?>
        <tr class="<?php echo strtolower($batch['company']); ?>">
          <td><?php echo form_checkbox("PENALTY[epids][".$key."]", $batch['epid'], set_value(form_error("PENALTY[epids][".$key."]"), false), ['class'=>strtolower($batch['company'])]); ?></td>
          <td><?php echo $batch['reference']; ?></td>
          <td><?php echo $batch['number_of_engines']; ?></td>
          <td><?php echo $batch['total_penalty']; ?></td>
          <td><?php echo $batch['company']; ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td></td>
          <td>No Result Found.</td>
          <td></td>
          <td></td>
          <td></td>
        </td>
      <?php endif; ?>
    </tbody>
  </table>
  <div id="ric-number-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-header" style="height:18px">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
      <div class="form-horizontal">
        <div class="control-group">
          <label class="control-label" for="ric-number">Input RIC Number:</label>
          <div class="controls">
            <?php echo form_input(['id'=>'ric-number', 'name'=>'PENALTY[ric_number]', 'value' => set_value("PENALTY[ric_number]",""), 'required'=>true]); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn" type="button" data-dismiss="modal" aria-hidden="true">Close</button>
      <button id="save-ric" class="btn btn-primary" type="submit" onclick="return confirm('Are you sure?')">Save RIC</button>
    </div>
  </div>
</form>

<script>
  $('input[type=checkbox]').on('click', function(e) {
    e.preventDefault;
    var disable = false;
    var company = $(this).attr('class');
    var show_or_hide = 'hide';

    if ($('input[type=checkbox]:checked').length === 0) {
      disable = true;
      show_or_hide = 'show';
    }

    showOrHideCompany(company, show_or_hide)
    $('#modal-btn').prop('disabled', disable);
  });

  $('#ric-number-modal').on('show', function() {
    setTimeout(function() {
      $('#ric-number').focus();
    }, 500);
  });

  function showOrHideCompany(company, method) {
    popCompany(company).forEach(
      function(company){
        switch (method) {
        case 'show':
          $('tr.'+company).show();
          break;
        case 'hide':
          $('tr.'+company).hide();
          break;
        }
      }
    );
  }

  function popCompany(company){
    var companies = ['mnc', 'mti', 'hpti', 'mdi'];
    var i = companies.indexOf(company);
    companies.splice(i, 1);

    return companies;
  }
</script>
