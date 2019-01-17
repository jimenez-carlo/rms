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
<div class="container-fluid">
	<div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Rerfo</div>
            </div>
            <div class="block-content collapse in">

                <!-- Search Form -->
<!--                 <form class="form-horizontal" method="post" style="margin:10px 0px;">
                    <fieldset>
                        <div class="row-fluid">

                            <?php if (isset($branch)) { ?>
                            <div class="control-group span4">
                                <div class="control-label">
                                    Branch
                                </div>
                                <div class="controls">
                                    <?php
                                    $options = array();
                                    foreach ($branch as $row)
                                    {
                                        $options[$row->bid] = $row->b_code.' '.$row->name;
                                    }
                                    print form_dropdown('branch', $options, set_value('branch'));
                                    ?>
                                </div>
                            </div>
                            <?php } ?>
                            <input type="submit" value="Search" name="search" class="btn btn-success">
                        </div>
                    </fieldset>
                </form> -->

                <!-- Table Form -->
                <?php if(!empty($rerfo)) { ?>

                <form class="form-horizontal" method="post" style="margin:0px;">
                    <fieldset>
                        <input type="hidden" name="rid" value="<?php print $rerfo->rid; ?>">
                        <input type="hidden" name="exp_date" value="<?php print $rerfo->date; ?>">
                        <input type="hidden" name="branch" value="<?php print $rerfo->branch->bid; ?>">

                        <br>
                        <table class="table" style="margin-bottom:0px;">
                            <thead>
                                <tr>
                                    <th><p>Date Sold</p></th>
                                    <th><p>Type of Sales</p></th>
                                    <th><p>Engine #</p></th>
                                    <th><p>Customer Code</p></th>
                                    <th><p>Customer Name</p></th>
                                    <th><p>Registration Type</p></th>
                                    <th><p>AR #</p></th>
                                    <th><p class="text-right">Amount Given</p></th>
                                    <th><p class="text-right">LTO Registration</p></th>
                                    <th><p class="text-right">LTO Tip</p></th>
                                    <th><p class="text-right">CR #</p></th>
                                    <th><p class="text-right">MV File #</p></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rerfo->sales as $sales) { ?>
                                <tr>
                                    <td><?php print $sales->date_sold; ?></td>
                                    <td><?php print $sales->sales_type; ?></td>
                                    <td><?php print $sales->engine_no; ?></td>
                                    <td><?php print $sales->cust_code; ?></td>
                                    <td><?php print $sales->first_name." ".$sales->last_name; ?></td>
                                    <td><p><?php print $sales->registration_type; ?></p></td>
                                    <td><?php print $sales->ar_no; ?></td>
                                    <td><p class="text-right">
                                        <?php print number_format($sales->amount,2,'.',','); ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print number_format($sales->registration,2,'.',','); ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print number_format($sales->tip,2,'.',','); ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->cr_no; ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->mvf_no; ?>
                                    </p></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot style="font-size: 20px">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th><p class="text-right">&#x20b1 <?php print number_format($rerfo->total_credit, 2, ".", ","); ?></p></th>
                                    <th><p class="text-right exp">&#x20b1 <?php print number_format($rerfo->total_registration, 2, ".", ","); ?></p></th>
                                    <th><p class="text-right exp">&#x20b1 <?php print number_format($rerfo->total_tip, 2, ".", ","); ?></p></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="form-actions span12" style="margin:0px;">
                            <?php
                            if ($rerfo->print == 0) {
                                print '<a href="./../sprint/'.$rerfo->rid.'" class="btn btn-success" onclick="return confirm('."'Are you sure you want to save and print?'".')">Print</a>';
                            }
                            else {
                                print '<a href="./../request/'.$rerfo->rid.'" class="btn btn-success">Request Reprinting</a>';
                            }
                            ?>
                        </div>

                    </fieldset>
                </form>

                <?php } ?>

			</div>
		</div>

	</div>
 </div>
