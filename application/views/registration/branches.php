<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
        <div class="block">
            <div class="navbar navbar-inner block-header">
                <div class="pull-left">Expense</div>
            </div>
            <div class="block-content collapse in">
                <form method="post" class="form-horizontal">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><p></p></th>
                                <th><p>Branch</p></th>
                                <th><p># of records for update</p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($table as $row)
                            {
                                print '<tr>';
                                print '<td>'.form_radio('bid', $row->branch->bid, FALSE).'</td>';
                                print '<td>'.$row->branch->b_code.' '.$row->branch->name.'</td>';
                                print '<td>'.$row->sales.'</td>';
                                print '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="form-actions">
                        <input type="submit" name="search" value="Search" class="btn btn-success">
                    </div>
                </form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function(){
    $(document).ready(function(){
        $('table input').click(function(){
            $('form').submit();
        });
    });
});
</script>