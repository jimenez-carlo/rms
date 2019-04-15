<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Registration</div>
      </div>
      <div class="block-content collapse in">
        <form method="post" class="form-horizontal">

          <!-- Search Form -->
          <fieldset>
            <div class="control-group span4">
              <div class="control-label">
                  NRU Date
              </div>
              <div class="controls">
                <?php print form_input('status_date', set_value('status_date'), array('class' => 'datepicker')); ?>
              </div>
            </div>
            <div class="form-actions span12">
              <input type="submit" class="btn btn-success" value="Search" name="search">
            </div>
          </fieldset>

          <?php if (!empty($sales)) { ?>
          <!-- Table Form -->
          <br>
          <table class="table">
            <thead>
              <tr>
                <th>Date Sold</th>
                <th>Engine #</th>
                <th>Customer Name</th>
                <th>Registration</th>
                <th>Tip</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($sales as $row)
              {
                $key = "[".$row->sid."]";
                ?>
                <tr>
                  <td><?php print $row->date_sold ?></td>
                  <td>
                    <?php
                    print form_hidden('sid'.$key, 
                      set_value('sid'.$key, $row->sid));
                    print $row->engine->engine_no;
                    ?>
                  </td>
                  <td>
                    <?php
                    print $row->customer->first_name
                      .' '.$row->customer->middle_name
                      .' '.$row->customer->last_name;
                    ?>
                  </td>
                  <td><?php print $row->registration; ?></td>
                  <td><?php print $row->tip; ?></td>
                  <td></td>
                </tr>
              <?php } ?>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b><?php print number_format($total_registration, 2, ".", ""); ?></b></td>
                  <td><b><?php print number_format($total_tip, 2, ".", ""); ?></b></td>
                </tr>
            </tbody>
          </table>
          <?php } ?>

        </form>
			</div>
		</div>

	</div>
 </div>