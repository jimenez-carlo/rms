<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid form-horizontal">
	<div class="row-fluid">

        <!-- Topsheet Block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">
                    Transaction # <?php print $topsheet->trans_no; ?>
                </div>
            </div>
            <div class="block-content collapse in">
                <fieldset>
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
                </fieldset>

                <!-- Table -->
                <table class="table tbl-sales" style="margin:0;">
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
                        foreach ($topsheet->sales as $sales)
                        {
                            // do not display if with batch
                            if ($sales->batch == 0)
                            {

                            $expense = $sales->registration + $sales->tip;
                            $balance = $sales->amount - $expense;

                            if ($sales->acct_status == 1) print '<tr class="sales warning">';
                            else if ($sales->acct_status == 2) print '<tr class="sales info">';
                            else print '<tr class="sales">';
                            ?>
                                <td><?php print $sales->branch->b_code.' '.$sales->branch->name; ?></td>
                                <td><?php print $sales->date_sold; ?></td>
                                <td><?php print $sales->engine_no; ?></td>
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

                                <div class="span8">
                                <?php
                                if (!empty($sales->files))
                                {
                                    foreach ($sales->files as $file)
                                    {
                                        $path = './../../rms_dir/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/'.$file;
                                        print '<img src="'.$path.'" style="margin:1em; border:solid; float:left; width:88%;">';
                                    }
                                }
                                else
                                {
                                    print "No attachments.";
                                }

                                // Remarks
                                if (!empty($sales->remarks))
                                {
                                    print '<div><b>REMARKS</b></div><hr>';
                                    foreach ($sales->remarks as $row)
                                    {
                                        print '
                                        <div>
                                            <p>'.$row->remarks.'</p>
                                            <p><i>by '.$row->remarks_name.' ('.$row->remarks_user.') on '.$row->remarks_date.'</i></p>
                                        </div>';
                                    }
                                }
                                ?>
                                </div>

                                <!-- Details -->
                                <div class="span4" style="position:fixed; top:10%; right:10%;">
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
                                            <?php print $sales->engine_no; ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="control-label">
                                            Customer Name
                                        </div>
                                        <div class="controls">
                                            <?php print $sales->first_name.' '.$sales->last_name; ?>
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

                                    <?php if ($sales->acct_status != 1) { ?>
                                    <div class="form-actions">
                                        <a href="../check/<?php print $sales->sid; ?>" class="btn btn-success" onclick="return confirm('Are you sure you want to confirm?');">Confirm</a>
                                        <a class="btn btn-warning" onclick="hold(<?php print $sales->sid; ?>)">Hold</a>
                                    </div>

                                    <?php } else { ?>
                                    <div class="control-group">
                                        <div class="control-label">
                                            Status
                                        </div>
                                        <div class="controls">
                                            <b>On Hold<b>
                                        </div>
                                    </div>
                                    <?php } ?>
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
            </div>
        </div>

        <!-- Miscellaneous Block -->
        <?php if ($topsheet->misc_status != 3) { ?>
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Miscellaneous</div>
            </div>
            <div class="block-content collapse in">
                <div class="span5">
                    <!-- Misc Attachments -->
                    <?php
                    if (!empty($topsheet->files))
                    {
                        foreach ($topsheet->files as $key => $file)
                        {
                            $path = $dir.'rms_dir/misc/'.$topsheet->tid.'_'.$topsheet->trans_no.'/'.$file;

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
                    <?php if (!empty($topsheet->batch)) { ?>
                    <div style="margin-bottom: 2em; float: right;">
                        <a href="../misc_check/<?php print $topsheet->tid; ?>" class="btn btn-success">Check</a>
                        <a class="btn btn-warning" onclick="hold(0)">Hold</a>
                    </div>
                    <?php } ?>

                    <table class="table misc">
                        <tbody>
                            <tr>
                                <td>Meal:</td>
                                <td><p class="text-right"><?php print $topsheet->meal; ?></p></td>
                            </tr>
                            <tr>
                                <td>Photocopy:</td>
                                <td><p class="text-right"><?php print $topsheet->photocopy; ?></p></td>
                            </tr>
                            <tr>
                                <td>Transportation:</td>
                                <td><p class="text-right"><?php print $topsheet->transportation; ?></p></td>
                            </tr>
                            <tr>
                                <td>Others:</td>
                                <td><p class="text-right"><?php print $topsheet->others; ?></p></td>
                            </tr>
                            <tr>
                                <th>Total Miscellaneous:</th>
                                <th><p class="text-right"><?php print $topsheet->total_misc; ?></p></th>
                            </tr>
                            <tr>
                                <td>Remarks:</td>
                                <td>
                                    <?php
                                    if (!empty($topsheet->misc->remarks))
                                    {
                                        foreach ($topsheet->misc->remarks as $row)
                                        {
                                            print '
                                            <div>
                                                <p>'.$row->remarks.'</p>
                                                <p><i>by '.$row->remarks_name.' ('.$row->remarks_user.') on '.$row->remarks_date.'</i></p>
                                            </div>
                                            <hr>';
                                        }
                                    }
                                    else
                                    {
                                        print '<div>No remarks.</div>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
			</div>
		</div>
        <?php } ?>
	</div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">Hold</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error hide">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <form action="#" id="form" class="form-horizontal">
        <div class="form-body">
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label" style="margin-right:10px;">Reason</label>
            <div class="controls reason">
                <select multiple name="reason[]">
                   <option value="1">Wrong Attachment</option>
                   <option value="2">Wrong Registration Amount</option>
                   <option value="3">Wrong Tip Amount</option>
                   <option value="4">Wrong CR #</option>
                   <option value="5">Wrong MVF #</option>
                   <option value="6">Wrong Plate #</option>
                   <option value="0" class="others">Others</option>
                </select>
            </div>
            <div class="controls reason0 hide">
                <select multiple name="reason[]">
                   <option value="1">Wrong Attachment</option>
                   <option value="2">Wrong Meal Amount</option>
                   <option value="3">Wrong Photocopy Amount</option>
                   <option value="4">Wrong Transportation Amount</option>
                   <option value="5">Wrong Others Amount</option>
                   <option value="0" class="others">Others</option>
                </select>
            </div>
          </div>

          <div class="form-group remarks hide" style="margin-bottom:15px;">
            <label class="control-label" style="margin-right:10px;">Remarks</label>
            <div class="controls">
                <textarea name="remarks"></textarea>
            </div>
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnWithdraw" onclick="save_hold()" class="btn btn-success">Hold</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->