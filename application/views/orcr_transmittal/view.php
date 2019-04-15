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
            	<form method="post">
	                <table class="table">
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
	                                print '
	                                <td>
	                                	<p>Not Received</p>
	                                    <textarea name="remarks'.$key.'" placeholder="New remarks" class="sid-'.$sales->sid.'"></textarea>
	                                    <br>
	                                    <input type="submit" name="reply'.$key.'" value="Reply" class="btn btn-success">
	                                </td>';
	                            }

	                            // <!-- REMARKS -->
	                            if (!empty($sales->orcr_remarks))
	                            {
	                                print '<td>';
	                                foreach ($sales->orcr_remarks as $row)
	                                {
	                                    print '
	                                        '.$row->remarks.'<br>
	                                        <i>by '.$row->remarks_name.'
	                                         ('.$row->remarks_user.')
	                                         on '.$row->remarks_date.'</i><hr>';
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
	            </form>
			</div>
		</div>

	</div>
 </div>