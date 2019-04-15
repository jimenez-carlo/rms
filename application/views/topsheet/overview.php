<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Topsheet # <?php print $topsheet->trans_no; ?></div>
            </div>
            <div class="block-content collapse in">

                <form class="form-horizontal" method="post" style="margin:10px 0px;">
                    <!-- Status Logs 
                    <div class="span6">
                        <?php
                        /*$count = count($topsheet->history);
                        for ($i = 0; $i < $count; $i++)
                        {
                            $acct_appr = false;
                            print '<div class="control-group">';

                            print '<div class="control-label">';
                            switch ($topsheet->history[$i]->status)
                            {
                                case 'For ORCR Checking': print "Submitted to Accounting"; break;
                                case 'For SAP Uploading': print "For Checking"; break;
                                case 'For Manager Approval': print "For SAP Uploading"; $acct_appr = true; break;
                                case 'For Voucher': print ($acct_appr) ? "For Manager Approval" : "For SAP Uploading"; break;
                                case 'For Check Issuance': print "For Voucher"; break;
                                case 'For Management Approval': print "Check issued"; break;
                                case 'For Check Deposit': print "Check approved"; break;
                                case 'Deposited': print "Check deposited"; break;
                            }
                            print '</div>';

                            print '<div class="controls">';
                            if ($i != 0 && $i != 7) print 'Done ';
                            print 'on '.$topsheet->history[$i]->status_date;
                            print '</div>';
                            print '</div>';
                        }

                        // current status
                        if ($topsheet->status != 'Deposited')
                        {
                            print '
                            <div class="control-group">
                                <div class="control-label">
                                    '.$topsheet->status.'
                                </div>
                                <div class="controls">
                                    <b>In Progress</b>
                                </div>
                            </div>';
                        }*/
                        ?>
                    </div>
                    -->
                    <fieldset>
                        <div class="row-fluid">
                            <div class="span12">

                    <div class="control-group span4">
                        <div class="control-label">
                            Date
                        </div>
                        <div class="controls">
                            <?php print $topsheet->date; ?>
                        </div>
                    </div>
                    <div class="control-group span4">
                        <div class="control-label">
                            Region
                        </div>
                        <div class="controls">
                            <?php print $topsheet->region; ?>
                        </div>
                    </div>
                    <div class="control-group span4">
                        <div class="control-label">
                            Company
                        </div>
                        <div class="controls">
                            <?php print $topsheet->company; ?>
                        </div>
                    </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Table -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th><p>Code</p></th>
                                <th><p>Branch</p></th>
                                <th><p>Expense Date</p></th>
                                <th><p class="text-right">Cash</p></th>
                                <th><p class="text-right">Expense</p></th>
                                <!--th><p class="text-right">Balance</p></th-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $registration = 0;
                            $tip = 0;
                            $count = 0;

                            // distribute misc expense
                            foreach ($topsheet->rerfo as $rerfo)
                            {
                                $count += count($rerfo->sales);
                            }
                            $quotient = bcdiv($topsheet->total_misc, $count, 2);
                            $remainder = $topsheet->total_misc - ($quotient * $count);

                            foreach ($topsheet->rerfo as $rerfo)
                            {
                                ?>
                                <tr>
                                    <td><?php print $rerfo->branch->b_code; ?></td>
                                    <td><?php print $rerfo->branch->name; ?></td>
                                    <td><?php print $rerfo->exp_date ; ?></td>
                                    <td><p class="text-right"><?php print number_format($rerfo->total_credit, 2, ".", ","); ?></p></td>
                                    <td><p class="text-right"><?php print number_format($rerfo->total_expense, 2, ".", ","); ?></p></td>
                                    <!--td><p class="text-right"><?php print number_format($rerfo->total_balance, 2, ".", ","); ?></p></td-->
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot style="font-size: 20px">
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th><p class="text-right">&#x20b1 <?php print number_format($topsheet->total_credit, 2, ".", ","); ?></p></th>
                                <th><p class="text-right exp">&#x20b1 <?php print number_format($topsheet->total_expense, 2, ".", ","); ?></p></th>
                                <!--th><p class="text-right exp"><?php print number_format($topsheet->total_balance, 2, ".", ","); ?></p></th-->
                            </tr>
                        </tfoot>
                    </table>

                  <div class="span5">
                    <!-- Misc Attachments -->
                    <?php
                    if (!empty($topsheet->files))
                    {
                        foreach ($topsheet->files as $key => $file)
                        {
                            $path = './../../rms_dir/misc/'.$topsheet->tid.'_'.$topsheet->trans_no.'/'.$file;
                            print '<div class="attachment" style="position:relative; z-index:0">';
                            print form_hidden('filekeys[]', $key);
                            print '<img src="'.$path.'" style="margin:1em; border:solid">';
                            print '</div>';
                        }
                    }

                    if (empty($topsheet->files))
                    {
                        print '<div class="attachment" style="position:relative">No attachments.</div>';
                    }
                    ?>

                  </div>

                    <!-- Misc Expense -->
                    <div class="span6" style="margin-left:5em">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Meal:</td>
                                    <td><p class="text-right"><?php print $topsheet->misc->meal; ?></p></td>
                                </tr>
                                <tr>
                                    <td>Photocopy:</td>
                                    <td><p class="text-right"><?php print $topsheet->misc->photocopy; ?></p></td>
                                </tr>
                                <tr>
                                    <td>Transportation:</td>
                                    <td><p class="text-right"><?php print $topsheet->misc->transportation; ?></p></td>
                                </tr>
                                <tr>
                                    <td>Others:</td>
                                    <td><p class="text-right"><?php print $topsheet->misc->others; ?></p></td>
                                </tr>
                                <tr>
                                    <th>Total Miscellaneous:</th>
                                    <th><p class="text-right">&#x20b1 <?php print $topsheet->total_misc; ?></p></th>
                                </tr>
                            </tbody>
                        </table>

                        <br>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>CASH</th>
                                    <th><p class="text-right total-amt">&#x20b1 <?php print number_format($topsheet->total_credit, 2, ".", ""); ?></p></th>
                                </tr>
                                <tr>
                                    <th>TOTAL EXPENSE</th>
                                    <th><p class="text-right total-exp">&#x20b1 <?php print number_format($topsheet->total_expense, 2, ".", ""); ?></p></th>
                                </tr>
                                <tr>
                                    <th>BALANCE</th>
                                    <th><p class="text-right total-bal">&#x20b1 <?php print number_format($topsheet->total_balance, 2, ".", ""); ?></p></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </form>
			</div>
		</div>

	</div>
 </div>