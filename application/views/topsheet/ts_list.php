<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">	
	<div class="row-fluid">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">Topsheet</div>
			</div>
			<div class="block-content collapse in">

        <!-- Search Form -->
        <form class="form-horizontal" method="post" style="margin:10px 0px;">
          <fieldset>
            <div class="row-fluid">
              <div class="control-group span4">
                <?php
                  echo form_label('Transaction #', 'trans_no', array('class' => 'control-label'));
                  echo '<div class="controls">';
                  echo form_input('trans_no', set_value('trans_no'), array('style' => 'width:100%'));
                  echo '</div>';
                ?>
              </div>
          
		          <div class="control-group span4" style="margin-bottom:0;">
		            <div class="control-label">Printed Date</div>
		            <div class="controls">
	              <input type="text" name="print_date" class="datepicker" value="<?php if(isset($_POST['print_date'])) print $_POST['print_date']; ?>">
		            </div>
		          </div>
              <input type="submit" class="btn btn-success" value="Search" name="search">

            </div>
          </fieldset>
        </form>

        <?php if(isset($table)) { ?>

				<table class="table" style="margin:0">
					<thead>
						<tr>
							<th>Transaction Number</th>
							<th>Company</th>
							<th>Registration Date</th>
							<th>Printed Date</th>
							<th>Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($table as $key => $topsheet)
						{
						print '
						<tr>
							<td>'.$topsheet->trans_no.'</td>
							<td>'.$topsheet->company.'</td>
							<td>'.$topsheet->date.'</td>
							<td>'.$topsheet->print_date.'</td>
							<td>'.$topsheet->status.'</td>
							<td><a href="./ts_view/'.$topsheet->tid.'" class="btn btn-success">View</a></td>
						</tr>';
						}

						if (empty($table))
						{
							print '
								<tr>
									<td>No result.</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>';
						}
						?>
					</tbody>
				</table>

				<?php } ?>
			</div>
		</div>
	</div>
</div>