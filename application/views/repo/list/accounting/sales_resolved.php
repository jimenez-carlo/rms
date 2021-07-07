<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
	.modal {
		position: fixed;
		width: 60%;
		top: 10% !important;
		left: 20%;
		margin-top: auto;
		/* Negative half of height. */
		margin-left: auto;
		/* Negative half of width. */
	}

	.tab-pane {
		border: 1px solid;
		border-color: #ddd #ddd #ddd #ddd;
		padding: 20px;
	}

	.tabs-right>.nav-tabs {
		float: right;
		margin-left: 0px;
	}

	img {
		width: auto;
		height: 250px;
	}
</style>
<div class="container-fluid">
	<div class="row-fluid">
		<!-- block -->
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="pull-left">Resolved Repo Sales List</div>
			</div>
			<div class="block-content collapse in">
				<?php if (!in_array($this->session->position_name, array('Branch Secretary', 'Branch Head1', 'Cash Custodian'))) : ?>
					<form class="form-horizontal" method="post">
						<div class="control-group span5">
							<div class="control-label">Branch</div>
							<div class="controls">
								<?php print form_dropdown('branch', array(0 => '- Please select a branch -') + $branch, set_value('branch')); ?>
							</div>
						</div>

						<div class="form-actions">
							<input type="submit" name="search" value="Search" class="btn btn-success">
							<button type="button" id="btn-submit" class="btn btn-sm btn-success">Submit</button>
						</div>
					</form>
				<?php endif; ?>

				<hr>

				<form id="table_form" method="post" class="form-horizontal">
					<table id="data-table" class="table">
						<thead>
							<tr>
								<th>
									<p><input type="checkbox" name="select_all"></p>
								</th>
								<th>
									<p>Date Sold</p>
								</th>
								<th>
									<p>Branch</p>
								</th>
								<th>
									<p>Customer Name</p>
								</th>
								<th>
									<p>Engine #</p>
								</th>
								<th>
									<p>Registration Type</p>
								</th>
								<th>
									<p>AR #</p>
								</th>
								<th>
									<p>Amount Given</p>
								</th>
								<th>
									<p>Registration Expense</p>
								</th>
								<th>
									<p>Topsheet</p>
								</th>
								<?php //if (in_array($this->session->position, [108])): 
								?>
								<th></th>
								<?php //endif; 
								?>
							</tr>
						</thead>
						<tbody>
							<?php
							// $sales_type = array(0 => 'Brand New (Cash)', 1 => 'Brand New (Installment)');
							// $post_sids = set_value('sid', array());
							foreach ($table as $row) {
								switch ($row->reg_type) {
									case 'RENEWAL':
										$registration = ($row->orcr_amt + $row->renewal_amt);
										break;
									case 'FOR TRANSFER':
										$registration = ($row->orcr_amt + $row->transfer_amt);
										break;
									default:
										$registration = ($row->orcr_amt + $row->renewal_amt + $row->transfer_amt);
										break;
								}
								print '<tr id="tr_id_' . $row->repo_sales_id . '">';
								print '<td><input type="checkbox" name="ids[]" value="' . $row->repo_sales_id . '"></td>';
								print '<td>' . substr($row->date_sold, 0, 10) . '</td>';
								print '<td>' . $row->bcode . ' ' . $row->bname . '</td>';
								print '<td>' . strtoupper($row->last_name . ', ' . $row->first_name . ' ' . $row->middle_name) . '</td>';
								print '<td>' . $row->engine_no . '</td>';
								print '<td>' . $row->reg_type . '</td>';
								print '<td>' . $row->ar_num . '</td>';
								print '<td style="text-align:right">' . number_format($row->ar_amt, 2) . '</td>';
								print '<td style="text-align:right">' . number_format($registration, 2) . '</td>';
								print '<td>' . $row->trans_no . '</td>';
								print '<td><button value="' . $row->repo_sales_id . '" type="button" class="btn btn-success btn-view-sales" data-title="Change Status - ' . $row->da . '">Change Status</button></td>';
								//       if (in_array($this->session->position, [108])) {
								// print '<td><a class="btn btn-success" onclick="resolve('.$row->sid.')">Resolve</a></td>';
								//       }
								print '</tr>';
							}

							if (empty($table)) {
								print '<tr>
	              <td colspan=20>No result.</td>
	            </tr>';
							}
							?>
						</tbody>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function resolve(sid) {
		$('#da_form input[name=sid]').val(sid);
		$('#da_form').submit();
	}

	var dataTable = document.getElementById('data-table');
	var checkItAll = dataTable.querySelector('input[name="select_all"]');
	var inputs = dataTable.querySelectorAll('tbody>tr>td>input');

	checkItAll.addEventListener('change', function() {
		if (checkItAll.checked) {
			inputs.forEach(function(input) {
				input.parentElement.parentElement.classList.add("success");
				input.checked = true;

			});
		} else {
			inputs.forEach(function(input) {
				input.parentElement.parentElement.classList.remove("success");
				input.checked = false;
			});
		}
	});

	var check_boxes = document.querySelectorAll('input[name="ids"]');
	check_boxes.forEach(function(check_boxes) {
		check_boxes.addEventListener("click", function(event) {
			if (event.target.checked) {
				event.target.parentElement.parentElement.classList.add("success");
			} else {
				event.target.parentElement.parentElement.classList.remove("success");
			}
		});
	});


	document.getElementById('btn-submit').addEventListener("click", function(event) {
		event.preventDefault();
		// all_checked.parentNode.parentNode.removeChild(all_checked);
		var form_submit = document.getElementById("table_form");
		var params = new FormData(form_submit);
		var id = event.target.value;
		params.append("action", 'submit_repo_sale');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (this.responseText != '') {
					var res = JSON.parse(this.responseText);
					if (res.type == 'success') {
						success(res.message);
						deleteRows();
					} else {
						error(res.message);
					}
				} else {
					error("Something Went Wrong Call Your Administrator For Assistance!");
				}
			}
		};
		xhr.open("POST", BASE_URL + "Request", true);
		xhr.send(params);
	});

	function deleteRows() {
		var table = document.getElementById('data-table');
		var checkboxes = document.getElementsByName('ids[]');
		for (i = checkboxes.length - 1; i >= 0; i--) {
			if (checkboxes[i].checked == true) {
				table.deleteRow(i + 1)
			}
		}
	}
</script>