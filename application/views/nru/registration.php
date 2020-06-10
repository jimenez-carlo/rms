<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style type="text/css">
table {
  font-size: 10pt;
}
input[type="text"] {
  height: 18px;
}
</style>

<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block span12">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">NRU</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal" style="margin:0" onkeypress="return event.keyCode != 13;">
          <?php print form_hidden('ltid', $transmittal->ltid); ?>
          <?php print form_hidden('company', $transmittal->company); ?>

          <table class="table">
            <thead>
              <tr>
                <?php if ($action !== 'View'): ?>
                <th class="pull-left"><input type="submit" name="submit" class="btn btn-success submit disabled" value="Submit" onclick="return confirm('Items with Registration Amount will proceed to the next process. Continue?');" disabled></th>
                <?php endif; ?>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <?php if ($action !== 'View'): ?>
                <th class="pull-right"><a class="btn btn-success">Apply to all</a></th>
                <th><?php print form_input('registration_all', set_value('registration_all', '0.00'), array('class' => 'registration_all numeric', 'style' => 'width:100px')); ?></th>
                <?php endif; ?>
              </tr>
              <tr>
                <th width="1px"><p>#</p></th>
                <th><p>Branch</p></th>
                <th><p>LTO Pending Date</p></th>
                <th><p>Date Sold</p></th>
                <th><p>Engine #</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Payment Method</p></th>
                <?php if ($action !== 'View'): ?>
                <th><p>Registration Expense</p></th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 1;
              foreach($transmittal->sales as $sales)
              {
                $key = "[".$sales->sid."]";
                $sales->fund = 1;

                print '<tr>';
                print '<td width="1px">'.$count.'</td>';
                print '<td>'.$sales->branch.'</td>';
                print '<td>'.$sales->pending_date.'</td>';
                print '<td>'.$sales->date_sold.'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td>'.$sales->customer_name.'</td>';
                print '<td>'.$transmittal->payment_method.'</td>';
                if ($action !== 'View') {
                  print '<td>';
                  print form_input('registration'.$key, set_value('registration'.$key, $sales->registration), array('class' => 'registration numeric', 'style' => 'width:100px'));
                  print '</td>';
                }
                print '</tr>';
               $count++;
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
  $(document).ready(function() {
    $("th a").click(function(){
      var registration = toFloat($(".registration_all").val());
      // var fund = $("input[name=fund_all]:checked").val();

      if (!registration) registration = "0.00";
      $(".registration_all").val(registration).change();
      $(".registration").val(registration).change();
      // $('.fund').removeAttr('checked');
      // $('.fund'+fund).click();
    });

    $('.registration').change(function(){
      var exp = 0;
      $('.registration').each(function(){
        exp += toFloat( $(this).val() );
      });
      if (exp > 0) $('.submit').removeClass('disabled').removeAttr('disabled');
      else $('.submit').addClass('disabled').attr('disabled', '');
    });

    // $('.fund_all.default').click();
    // $('.fund1').click();
    $('.registration:last-child').change();
  });
});
</script>
