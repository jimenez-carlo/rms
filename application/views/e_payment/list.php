<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
        <div class="row-fluid">
                <div class="block">
                        <div class="navbar navbar-inner block-header">
                                <div class="pull-left">E-Payment Batch List</div>
                        </div>
                        <div class="block-content collapse in">
                                <form class="form-horizontal" method="post">
                                        <fieldset>
                                                <div class="control-group span5">
                                                  <div class="control-label">Date</div>
                                                  <div class="controls">
                                                    <span style="display: inline-block; width: 50px">From:</span>
                                                    <?php print form_input('date_from', set_value('date_from', date('Y-m-d', strtotime('-5 days'))), array('class' => 'datepicker', 'autocomplete' => 'off')); ?>
                                                    <br>
                                                    <span style="display: inline-block; width: 50px">To:</span>
                                                    <?php print form_input('date_to', set_value('date_to', date('Y-m-d')), array('class' => 'datepicker', 'autocomplete' => 'off')); ?>
                                                  </div>
                                                </div>

                                                <div class="control-group span5">
                                                        <div class="control-label">Region</div>
                                                        <div class="controls">
                                                                <?php
                                                                if($acctg){
                                                                $region = array_merge(array(0 => '- Any -'), $region);
                                                                print form_dropdown('region', $region, set_value('region'));
                                                                }else{
                                                                print form_dropdown('region', $region, set_value('region', $_SESSION['region_id']), array('readonly'=>'true'));
                                                                }
                                                                ?>
                                                        </div>
                                                </div>

                                                <div class="control-group span5">
                                                        <div class="control-label">Status</div>
                                                        <div class="controls">
                                                                <?php print form_dropdown('status', array_merge(array(0 => '- Any -'), $status), set_value('status')); ?>
                                                        </div>
                                                </div>

                                                <div class="form-actions span5">
                                                        <input type="submit" class="btn btn-success" value="Search" name="search">
                                                        <?php if(!$acctg): ?>
                                                        <a href="electronic_payment/add" target="_blank" class="btn btn-success">Add New Batch</a>
                                                        <?php endif; ?>
                                                </div>

                                                <div class="control-group span5">
                                                        <div class="control-label">Payment Reference #</div>
                                                        <div class="controls">
                                                                <?php print form_input('reference', set_value('reference')); ?>
                                                        </div>
                                                </div>
                                        </fieldset>

                                        <hr>

                                        <table class="table table-condensed">
                                                <thead>
                                                        <tr>
                                                                <th><p>Date</p></th>
                                                                <th><p>Region</p></th>
                                                                <th><p>E-Payment Reference #</p></th>
                                                                <th><p>Amount</p></th>
                                                                <th><p>RIC Reference #</p></th>
                                                                <th><p>RIC Amount</p></th>
                                                                <th><p>Pending Amount</p></th>
                                                                <th><p>For Liquidation</p></th>
                                                                <th><p>Liquidated</p></th>
                                                                <?php if(in_array($_SESSION['dept_name'],['Accounting','Treasury'])): ?>
                                                                <th><p>Document #</p></th>
                                                                <th><p>Debit Memo #</p></th>
                                                                <?php endif; ?>
                                                                <th><p>Payment Status</p></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                        <?php
                                                        foreach ($table as $row)
                                                        {
                                                                print '<tr>';
                                                                print '<td>'.$row->ref_date.'</td>';
                                                                print '<td>'.$row->region.' '.$row->company.'</td>';
                                                                print '<td><a href="electronic_payment/view/'.$row->epid.'" target="_blank">'.$row->reference.'</a></td>';
                                                                print '<td>'.number_format($row->ttl_amt, 2, '.', ',').'</td>';
                                                                print '<td>'.$row->reference_num.'</td>';
                                                                print '<td>'.$row->ric_penalty_amount.'</td>';
                                                                print '<td>'.$row->pending_amt.'</td>';
                                                                print '<td>'.$row->for_liq.'</td>';
                                                                print '<td>'.$row->liquidated.'</td>';
                                                                if (in_array($_SESSION['dept_name'],['Accounting','Treasury'])) {
                                                                  if (empty($row->doc_no)) print '<td>Pending</td>';
                                                                  else print '<td>'.$row->doc_no.'<br><i>on '.$row->doc_date.'</i></td>';
                                                                  if (empty($row->dm_no)) print '<td>-</td>';
                                                                  else print '<td>'.$row->dm_no.'<br><i>on '.$row->dm_date.'</i></td>';
                                                                }
                                                                print '<td>'.$status[$row->status].'</td>';
                                                                print '</tr>';
                                                        }

                                                        if (empty($table))
                                                        {
                                                                print '<tr>';
                                                                print '<td>No result.</td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
                                                                print '<td></td>';
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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog" style="width: 85%; left: 30%;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">&nbsp;</h3>
      </div>
      <div class="modal-body"><img></div>
      <div class="modal-footer"></div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(function(){
        $(document).ready(function(){
                $(".table").dataTable({
                        "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
                        "sPaginationType": "bootstrap",
                        "oLanguage": {
                                "sLengthMenu": "_MENU_ records per page"
                        },
                        "bFilter": false,
                        "bSort": false,
                        "iDisplayLength": 5,
                        "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
                });

                $(".table").on('click', 'a.receipt', function(){
                  $('#modal_form .modal-body img').attr('src', '/rms_dir/lto_receipt/<?php print $payment->epid.'/'.$payment->receipt; ?>');
                  $('#modal_form').modal('show');
                });
        });
});
</script>
