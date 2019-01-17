<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Transmittal</div>
            </div>
            <div class="block-content collapse in">

                <form class="form-horizontal" method="post" style="margin:10px 0;">
                  <fieldset>

                    <!-- Search Form -->
                    <div class="control-group">
                      <?php
                        echo form_label('Track #', 'trans_no', array('class' => 'control-label'));
                        echo '<div class="controls">';
                        echo form_input('trans_no', set_value('trans_no'));
                        echo '<input type="submit" class="btn btn-success" value="Search" name="search">';
                        echo '</div>';
                      ?>
                    </div>

                    <!-- Rerfo View -->
                    <?php if (isset($transmittal)) { ?>
                    <table class="table" style="margin:0;">
                        <thead>
                            <tr>
                                <th>CR #</th>
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($transmittal->sales as $sales)
                            {
                                $key = '['.$sales->sid.']';

                                print '<tr>';
                                print '<td>'.$sales->cr_no.'</td>';
                                print '<td>'.$sales->customer->last_name.", "
                                    .$sales->customer->first_name.'</td>';

                                if (!empty($sales->receive))
                                {
                                    print '<td>Received on '.$sales->receive->status_date.'</td>';
                                }
                                else
                                {
                                    $check0 = (empty($sales->remarks)) ? 1 : 0;
                                    $check1 = (set_value('receive'.$key, $check0) == 1) ? 'checked': '';
                                    $check2 = (set_value('receive'.$key, $check0) == 0) ? 'checked': '';

                                    print '
                                    <td>
                                        <input type="radio" class="receive" name="receive'.$key.'" value=1 '.$check1.'> Receive
                                        <br>
                                        <input type="radio" class="reject" name="receive'.$key.'" value=0 '.$check2.'> Not received
                                        <br><br>
                                        <textarea name="remarks'.$key.'" placeholder="New remarks" class="hide sid-'.$sales->sid.'"></textarea>
                                    </td>';
                                }

                                // <!-- REMARKS -->
                                if (!empty($sales->remarks))
                                {
                                    print '<td>';
                                    foreach ($sales->remarks as $row)
                                    {
                                        print '
                                            '.$row->remarks.'<br>
                                            <i>by '.$row->user->firstname.' ('.$row->user->username.')
                                             on '.$row->date.'</i><hr>';
                                    }
                                    print '</td>';
                                }
                                else
                                {
                                    print '<td>No Remarks</td>';
                                }   

                                print '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="form-actions span12">
                      <input type="submit" class="btn btn-success" value="Save" name="submit">
                    </div>

                    <?php } else { ?>

                    <!-- Rerfo List -->
                    <table class="table" style="margin:0;">
                        <thead>
                            <tr>
                                <th>Track #</th>
                                <th>Sales Type</th>
                                <th>Date Sent</th>
                                <th>Sent by</th>
                            </tr> 
                        </thead>
                        <tbody>
                            <?php
                            foreach ($table as $transmittal) {
                                print "<tr>";
                                print "<td><a class=trans_no>".$transmittal->trans_no."</a></td>";
                                print "<td>";
                                ($transmittal->type ? print 'Installment' : print 'Cash');
                                print "</td>";
                                print "<td>".$transmittal->date."</td>";
                                print "<td>".$transmittal->user."</td>";
                                print "</tr>";
                            }

                            if (empty($table))
                            {
                                print "<tr><td colspan=10>No Result.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php } ?>

                  </fieldset>
                </form>

			</div>
		</div>
    </div>
</div>