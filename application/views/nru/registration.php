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
                <th class="pull-left"><input type="submit" name="submit" class="btn btn-success submit disabled" value="Submit" onclick="return confirm('Items with Registration Amount will proceed to the next process. Continue?');" disabled></th>
                <th></th>
                <th></th>
                <th></th>
                <th class="pull-right"><a class="btn btn-success">Apply to all</a></th>
                <th><?php print form_input('registration_all', set_value('registration_all', '0.00'), array('class' => 'registration_all numeric', 'style' => 'width:100px')); ?></th>
                <!-- <th>
                  <?php
                  print '
                    <span style="display: inline-block;"><input type="radio" name="fund_all" value="2" class="fund_all"> Cash</span>
                    <span style="display: inline-block; margin-left: 1em"><input type="radio" name="fund_all" value="1" class="fund_all default" checked> Check</span>';
                  ?>
                </th> -->
              </tr>
              <tr>
                <th><p>Branch</p></th>
                <th><p>LTO Pending Date</p></th>
                <th><p>Date Sold</p></th>
                <th><p>Engine #</p></th>
                <th><p>Customer Name</p></th>
                <th><p>Registration Expense</p></th>
                <!-- <th><p>Payment Method</p></th> -->
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($transmittal->sales as $sales)
              {
                $key = "[".$sales->sid."]";
                $sales->fund = 1;

                print '<tr>';
                print '<td>'.$sales->bcode.' '.$sales->bname.'</td>';
                print '<td>'.$sales->pending_date.'</td>';
                print '<td>'.substr($sales->date_sold, 0, 10).'</td>';
                print '<td>'.$sales->engine_no.'</td>';
                print '<td>'.$sales->first_name.' '.$sales->last_name.'</td>';

                print '<td>';
                // print form_hidden('fund'.$key, 2);
                print form_input('registration'.$key, set_value('registration'.$key, $sales->registration), array('class' => 'registration numeric', 'style' => 'width:100px'));
                print '</td>';

                // print '<td>
                //   <span style="display: inline-block;">'.form_radio('fund'.$key, 2, (set_value('fund'.$key, $sales->fund) == 2), array('class' => 'fund fund2')).' Cash</span>
                //   <span style="display: inline-block; margin-left: 1em">'.form_radio('fund'.$key, 1, (set_value('fund'.$key, $sales->fund) == 1), array('class' => 'fund fund1')).' Check</span>
                //   </td>';
                print '</tr>';
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