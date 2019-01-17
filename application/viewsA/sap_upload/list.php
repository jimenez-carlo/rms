<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
	<div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">For SAP Uploading</div>
      </div>
      <div class="block-content collapse in">
        <form method="post">
        <table class="table" style="margin:0;">
          <thead>
            <tr>
              <th><p>Download</p></th>
              <th><p>Batch #</p></th>
              <th><p>Date</p></th>
              <th><p>Region</p></th>
              <th><p>Company</p></th>
              <th><p>Downloaded Date</p></th>
              <th><p>Document Number</p></th>
              <th><p></p></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $id_index = 0;
            foreach($table as $row)
            {
              print '<tr>';
              print '<td><center><a href="sap_upload/sap/'.$row->bid.'" id="download" onclick="set_id('.$id_index.')"><span class="icon icon-download" style="font-size:125%;"></span></a></center></td>';
              print '<td>'.$row->trans_no.'</td>';
              print '<td>'.$row->post_date.'</td>';
              print '<td>'.$row->region.'</td>';
              print '<td>'.$row->company.'</td>';
              print '<td>'.$row->download_date.'</td>';//['.$row->bid.']
              print '<td><input type="text" name="doc_no" class="doc_no" disabled></td>';
              print '<td><input type="hidden" name="bid" value="'.$row->bid.'"></td>';
              print '<td><input type="submit" class="btn btn-success save" value="Save" name="save" disabled></td>';
              //print '<td><a onclick="save('.$row->bid.')" class="btn btn-success save" disabled>Save</a></td>';
              print '</tr>';
              $id_index++;
            }

            if (empty($table))
            {
              print '<tr>
                <td>No result.</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">Save</h3>
    </div>
    <div class="modal-body form">
      <div class="alert alert-error">
        <button class="close" data-dismiss="alert">&times;</button>
        <div class="error"></div>
      </div>
      <form action="#" id="form" class="form-horizontal">
        <div class="form-body">
          <div class="form-group" style="margin-bottom:15px;">
            <label class="control-label" style="margin-right:10px;">Document #</label>
            <div class="controls"><input type="text" name="doc_no"></div>
          </div>
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnWithdraw" onclick="save_doc()" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->