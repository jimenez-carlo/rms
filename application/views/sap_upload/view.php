<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Batch # <?php print $batch->trans_no; ?></div>
            </div>
            <div class="block-content collapse in">

                <form class="form-horizontal" method="post">
                    <fieldset>
                        <div class="control-group">
                            <div class="control-label">
                                Document #
                            </div>
                            <div class="controls">
                                <?php print (!empty($batch->doc_no)) ? $batch->doc_no : form_input('doc_no', set_value('doc_no')); ?>
                            </div>
                        </div>

                        <?php if (empty($batch->doc_no)) { ?>
                        <div class="form-actions">
                            <input type="submit" name="save" value="Save" class="btn btn-success">
                            <a href="../sap/<?php print $batch->bid; ?>" class="btn btn-success">SAP Template</a>
                        </div>
                        <?php } ?>

                        <hr>
                        <div class="span12">
                            <div class="control-group span4">
                                <div class="control-label">
                                    Rerfo Date
                                </div>
                                <div class="controls">
                                    <?php print $batch->batch_date; ?>
                                </div>
                            </div>
                            <div class="control-group span4">
                                <div class="control-label">
                                    Region
                                </div>
                                <div class="controls">
                                    <?php print $batch->region->name; ?>
                                </div>
                            </div>
                            <div class="control-group span4">
                                <div class="control-label">
                                    Company
                                </div>
                                <div class="controls">
                                    <?php print $batch->company->code; ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Table -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th><p>Branch</p></th>
                                <th><p>Date Sold</p></th>
                                <th><p>Engine #</p></th>
                                <th><p>Type of Sales</p></th>
                                <th><p>Registration Type</p></th>
                                <th><p>AR #</p></th>
                                <th><p class="text-right">Amount Given</p></th>
                                <th><p class="text-right">LTO Registration</p></th>
                                <th><p class="text-right">LTO Tip</p></th>
                                <th><p class="text-right">Total Expense</p></th>
                                <th><p class="text-right">Balance</p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $registration = 0;
                            $tip = 0;
                            $total_credit = 0;
                            $total_expense = 0;
                            $total_balance = 0;
                            foreach ($batch->sales as $sales)
                            {
                                $expense = $sales->registration + $sales->tip;
                                $balance = $sales->amount - $expense;
                                ?>

                                <tr class="sales">
                                    <td><?php print $sales->branch->b_code.' '.$sales->branch->name; ?></td>
                                    <td><?php print $sales->date_sold; ?></td>
                                    <td><?php print $sales->engine->engine_no; ?></td>
                                    <td><?php print $sales->sales_type; ?></td>
                                    <td><?php print $sales->registration_type; ?></td>
                                    <td><?php print $sales->ar_no; ?></td>
                                    <td><p class="text-right">
                                        <?php print $sales->amount; ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->registration; ?>
                                    </p></td>
                                    <td><p class="text-right">
                                        <?php print $sales->tip; ?>
                                    </p></td>
                                    <td><p class="text-right"><?php print number_format($expense, 2, ".", ""); ?></p></td>
                                    <td><p class="text-right"><?php print number_format($balance, 2, ".", ""); ?></p></td>
                                </tr>

                                <!-- Attachments -->
                                <tr class="attachments hide">
                                    <td style="background:white; position:fixed; top:5%; bottom:5%; left:0; right:0; border-top:none; overflow-y:scroll; z-index:1">
                                    <a class="close" style="position:fixed; top:15%; right:5%;">X</a>

                                    <div class="span6">
                                    <?php
                                    if (!empty($sales->files))
                                    {
                                        foreach ($sales->files as $file)
                                        {
                                            $path = './../../rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine->engine_no.'/'.$file;
                                            print '<img src="'.$path.'" style="margin:1em; border:solid; float:left; width:100%; height:500px">';
                                        }
                                    }
                                    else
                                    {
                                        print "No attachments.";
                                    }
                                    ?>
                                    </div>

                                    <!-- Details -->
                                    <div class="span4" style="position:fixed; top:15%; right:10%;">
                                        <div class="control-group">
                                            <div class="control-label">
                                                Branch
                                            </div>
                                            <div class="controls">
                                                <?php print $sales->branch->b_code.' '.$sales->branch->name; ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="control-label">
                                                Engine #
                                            </div>
                                            <div class="controls">
                                                <?php print $sales->engine->engine_no; ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="control-label">
                                                Customer Name
                                            </div>
                                            <div class="controls">
                                                <?php print $sales->customer->first_name.' '.$sales->customer->last_name; ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="control-label">
                                                Registration
                                            </div>
                                            <div class="controls">
                                                <?php print $sales->registration; ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="control-label">
                                                Tip
                                            </div>
                                            <div class="controls">
                                                <?php print $sales->tip; ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="control-label">
                                                CR #
                                            </div>
                                            <div class="controls">
                                                <?php print $sales->cr_no; ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="control-label">
                                                MVF #
                                            </div>
                                            <div class="controls">
                                                <?php print $sales->mvf_no; ?>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="control-label">
                                                Plate #
                                            </div>
                                            <div class="controls">
                                                <?php print $sales->plate_no; ?>
                                            </div>
                                        </div>
                                    </div>

                                    </td>
                                </tr>

                                <?php
                                $registration += $sales->registration;
                                $tip += $sales->tip;
                                $total_credit += $sales->amount;
                                $total_expense += $expense;
                                $total_balance += $balance;
                            }
                            ?>
                        </tbody>
                        <tfoot style="font-size: 20px">
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th><p class="text-right"><?php print number_format($total_credit, 2, ".", ""); ?></p></th>
                                <th><p class="text-right"><?php print number_format($registration, 2, ".", ""); ?></p></th>
                                <th><p class="text-right"><?php print number_format($tip, 2, ".", ""); ?></p></th>
                                <th><p class="text-right"><?php print number_format($total_expense, 2, ".", ""); ?></p></th>
                                <th><p class="text-right"><?php print number_format($total_balance, 2, ".", ""); ?></p></th>
                            </tr>
                        </tfoot>
                    </table>

                </form>
			</div>
		</div>

	</div>
 </div>