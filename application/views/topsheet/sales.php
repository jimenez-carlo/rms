<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Topsheet</div>
            </div>
            <div class="block-content collapse in">

                <form class="form-horizontal" method="post">
                    <fieldset>

                        <div class="row-fluid">
                            <div class="control-group span4">
                                <div class="control-label">
                                    Transaction #
                                </div>
                                <div class="controls">
                                    <?php print $topsheet->trans_no; ?>
                                </div>
                            </div>
                            <div class="control-group span4">
                                <div class="control-label">
                                    Rerfo Date
                                </div>
                                <div class="controls">
                                    <?php print $topsheet->rerfo_date; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row-fluid">
                            <div class="control-group span4">
                                <div class="control-label">
                                    Region
                                </div>
                                <div class="controls">
                                    <?php print $topsheet->region->name; ?>
                                </div>
                            </div>
                            <div class="control-group span4">
                                <div class="control-label">
                                    Company
                                </div>
                                <div class="controls">
                                    <?php print $topsheet->company->code; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><p>Branch</p></th>
                                    <th><p>Expense Date</p></th>
                                    <th><p>Date Sold</p></th>
                                    <th><p>Type of Sales</p></th>
                                    <th><p>Customer Name</p></th>
                                    <th><p>Engine #</p></th>
                                    <th><p>AR #</p></th>
                                    <th><p>Registration Type</p></th>
                                    <th><p class="text-right">Amount Given</p></th>
                                    <th><p class="text-right">LTO Registration</p></th>
                                    <th><p class="text-right">LTO Tip</p></th>
                                    <th><p class="text-right">CR #</p></th>
                                    <th><p class="text-right">MV File #</p></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($topsheet->rerfo as $rerfo)
                                {
                                    foreach ($rerfo->sales as $sales)
                                    {
                                ?>
                                <tr>
                                    <td><?php print $rerfo->branch->b_code.' '.$rerfo->branch->name; ?></td>
                                    <td><?php print $rerfo->exp_date ; ?></td>
                                    <td><?php print $sales->date_sold; ?></td>
                                    <td><?php print $sales->sales_type; ?></td>
                                    <td><?php print $sales->customer->first_name
                                        ." ".$sales->customer->middle_name
                                        ." ".$sales->customer->last_name ; ?></td>
                                    <td><?php print $sales->engine->engine_no; ?></td>
                                    <td><?php print $sales->ar_no; ?></td>
                                    <td><p><?php print $sales->registration_type; ?></p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->amount; ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->registration; ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->tip; ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->cr_no; ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->mvf_no; ?>
                                    </p></td>
                                </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th><p class="text-right"><?php print number_format($rerfo->total_credit, 2, ".", ""); ?></p></th>
                                    <th><p class="text-right exp"><?php print number_format($rerfo->total_registration, 2, ".", ""); ?></p></th>
                                    <th><p class="text-right exp"><?php print number_format($rerfo->total_tip, 2, ".", ""); ?></p></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                        <br>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Expense Date</th>
                                    <th>Meal</th>
                                    <th>Photocopy</th>
                                    <th>Transportation</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $meal = 0;
                                $photocopy = 0;
                                $transportation = 0;
                                foreach ($topsheet->rerfo as $rerfo)
                                {
                                ?>

                                <tr>
                                    <td><?php print $rerfo->branch->b_code.' '.$rerfo->branch->name; ?></td>
                                    <td><?php print $rerfo->exp_date ; ?></td>
                                    <td><p class="text-right"><?php print $rerfo->misc->meal; ?></p></td>
                                    <td><p class="text-right"><?php print $rerfo->misc->photocopy; ?></p></td>
                                    <td><p class="text-right"><?php print $rerfo->misc->transportation; ?></p></td>
                                    <th><p class="text-right"><?php print $rerfo->total_misc; ?></p></th>
                                </tr>

                                <?php
                                    $meal = $rerfo->misc->meal;
                                    $photocopy = $rerfo->misc->photocopy;
                                    $transportation = $rerfo->misc->transportation;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Total</th>
                                    <th><?php print $meal; ?></th>
                                    <th><?php print $photocopy; ?></th>
                                    <th><?php print $transportation; ?></th>
                                    <th><?php print $topsheet->total_misc; ?></th>
                                </tr>
                            </tfoot>
                        </table>

                        <br>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>CASH</th>
                                    <th><p class="text-right total-amt"><?php print number_format($topsheet->total_credit, 2, ".", ""); ?></p></th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>TOTAL EXPENSE</th>
                                    <th><p class="text-right total-exp"><?php print number_format($topsheet->total_expense, 2, ".", ""); ?></p></th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>BALANCE</th>
                                    <th><p class="text-right total-bal"><?php print number_format($topsheet->total_balance, 2, ".", ""); ?></p></th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="form-actions span12">
                            <div style="float:right;padding-right:5em">
                                <?php $link = (isset($rrt)) ? 'view/'.$topsheet->tid : 'topsheet/view/'.$topsheet->tid ?>
                                <a href="<?php print $link; ?>">View Summary</a>
                            </div>
                        </div>

                    </fieldset>
                </form>

			</div>
		</div>

	</div>
 </div>