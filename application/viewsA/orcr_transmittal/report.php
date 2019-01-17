<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">OR CR Transmittal</div>
            </div>
            <div class="block-content collapse in">


                <!-- Search Form -->
                <form class="form-horizontal" method="post" style="margin:10px 0;">
                    <fieldset>
                        <div class="row-fluid">
                            <?php if (isset($branch)) { ?>
                            <div class="control-group span4" style="margin-bottom:0px;">
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
                            <div class="control-group span4" style="margin-bottom:0px;">
                                <div class="control-label">
                                    Status
                                </div>
                                <div class="controls">
                                    <select name="status">
                                        <option value="0">- All -</option>
                                        <option value="1">For Transmittal</option>
                                        <option value="2">Transmitted</option>
                                        <option value="3">Received</option>
                                        <option value="4">Not Received</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group span4" style="margin-bottom:0px;">
                              <input type="submit" value="Search" name="search" class="btn btn-success">
                              <a href="<?php if(isset($dir)) echo $dir; ?>orcr_transmittal/report" class="btn btn-success">Summary</a>
                            </div>
                        </div>
                    </fieldset>
                </form>

			</div>
		</div>

	</div>
 </div>

<?php if(isset($transmittal)) { ?>
<div class="container-fluid">
    <div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Result</div>
            </div>
            <div class="block-content collapse in">

                <table cellpadding="0" cellspacing="0" border="0" class="table" style="margin:0">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Customer Name</th>
                            <th>CR #</th>
                            <th>Track #</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( count($transmittal) > 0 ): ?>
                            <?php foreach($transmittal as $tr): ?>
                                <tr>
                                    <td><?php echo $options[$tr->branch]; ?></td>
                                    <td><?php echo $tr->customer_name; ?></td>
                                    <td><?php echo $tr->cr_no; ?></td>
                                    <td><a href="view/<?php print $tr->ttid ?>"><?php echo $tr->track_no; ?></a></td>
                                    <td><?php echo $tr->status; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td>No results found.</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>   
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
 </div>
 <?php } ?>

<?php if(isset($branches)) { ?>
<div class="container-fluid">
    <div class="row-fluid">
        <!-- block -->
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Summary</div>
            </div>
            <div class="block-content collapse in">

            <table class="table summary">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Branch</th>
                  <th>For Transmittal</th>
                  <th>Transmitted</th>
                  <th>Received</th>
                  <th>Not Received</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $pending = 0;
                $transmitted = 0;
                $received = 0;
                $unreceived = 0;
                foreach ($branches as $branch) 
                {
                  $pending += $branch->pending;
                  $transmitted += $branch->transmitted;
                  $received += $branch->received;
                  $unreceived += $branch->unreceived;
                  $total = $branch->pending
                    + $branch->transmitted
                    + $branch->received
                    + $branch->unreceived;
                  if ($total > 0)
                  {
                    print '
                    <tr>
                      <td>'.$branch->b_code.'</td>
                      <td>'.$branch->name.'</td>
                      <td><a class="'.$branch->bid.'-1">'.$branch->pending.'</a></td>
                      <td><a class="'.$branch->bid.'-2">'.$branch->transmitted.'</a></td>
                      <td><a class="'.$branch->bid.'-3">'.$branch->received.'</a></td>
                      <td><a class="'.$branch->bid.'-4">'.$branch->unreceived.'</a></td>
                    </tr>';
                  }
                }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th></th>
                  <th>Total</th>
                  <th><?php print $pending; ?></th>
                  <th><?php print $transmitted; ?></th>
                  <th><?php print $received; ?></th>
                  <th><?php print $unreceived; ?></th>
                </tr>
              </tfoot>
            </table>

            </div>
        </div>

    </div>
 </div>
 <?php } ?>