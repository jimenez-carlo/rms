<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block">
      <div class="navbar navbar-inner block-header">
          <div class="pull-left">Disapprove List</div>
      </div>
      <div class="block-content collapse in">
      	<form class="form-horizontal" method="post">
      		<div class="control-group span5">
      			<div class="control-label">Branch</div>
      			<div class="controls">
      				<?php print form_dropdown('branch', array(0 => '- Please select a branch -') + $branch, set_value('branch')); ?>
      			</div>
      		</div>
      		<div class="form-actions">
      			<input type="submit" name="search" value="Search" class="btn btn-success">
                        <button id="save_resolve" class="btn btn-success" style="position:absolute; right:60px;" disabled>Submit</button>
      		</div>
      	</form>
      	<hr>
        <table id="da_table" class="table">
          <thead>
            <tr>
              <th><p>Date Sold</p></th>
              <th><p>Branch</p></th>
              <th><p>Customer Name</p></th>
              <th><p>Engine #</p></th>
              <th><p>SI #</p></th>
              <th><p>Registration Type</p></th>
              <th><p>AR #</p></th>
              <th><p>Registration Expense</p></th>
              <th><p>CR #</p></th>
              <th><p>Topsheet</p></th>
              <th><p>Disapprove Status</p></th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sales_type = array(0 => 'Brand New (Cash)', 1 => 'Brand New (Installment)');
          $post_sids = set_value('sid', array());
          foreach ($table as $row)
          {
            print '<tr id="'.$row->sid.'">';
            print '<td>'.substr($row->date_sold, 0, 10).'</td>';
            print '<td>'.$row->bcode.' '.$row->bname.'</td>';
            print '<td>'.$row->first_name.' '.$row->last_name.'</td>';
            print '<td>'.$row->engine_no.'</td>';
            print '<td>'.$row->si_no.'</td>';
            print '<td>'.$row->registration_type.'</td>';
            print '<td>'.$row->ar_no.'</td>';
            print '<td>'.$row->registration.'</td>';
            print '<td>'.$row->cr_no.'</td>';
            print '<td>'.$row->trans_no.'</td>';
            print '<td>'.$row->da_status.'</td>';
            print '</tr>';
          }

          if (empty($table))
          {
            print '<tr>
              <td colspan=20>No result.</td>
            </tr>';
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_form" role="dialog" style="width: 85%; left: 30%;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">View attachment</h3>
      </div>
      <div class="modal-body form-horizontal" style="height: 430px;">
        <div class="alert alert-error hide">
          <button class="close" data-dismiss="alert">&times;</button>
          <div class="error"></div>
        </div>
        <div class="form-body">
          <!-- see attachment.php -->
        </div>
      </div>
      <div class="modal-footer">
        <button id="include_for_upload" type="button" class="btn btn-success include">Include For Upload</button>
        <button id="exclude_for_upload" type="button" class="btn btn-success exclude">Exclude For Upload</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
  var sales_id = '';
  var sales_ids = new Array();

  $("tr").on("click", function(e){
    sales_id = this.id;
    var bool = find(sales_ids, sales_id); // footer.php
    if (bool) {
      $("#include_for_upload").prop("disabled", true).addClass("hide");
      $("#exclude_for_upload").prop("disabled", false).removeClass("hide");
    } else {
      $("#include_for_upload").prop("disabled", false).removeClass("hide");
      $("#exclude_for_upload").prop("disabled", true).addClass("hide");
    }

    if (sales_id) {
      $.ajax({
        url : "<?php echo base_url(); ?>orcr_checking/attachment",
        data: {"type": 1, "id": sales_id},
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
          $(".form-body").html(data.page);
	  $("#modal_form").modal("show");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          alert('Error get data from ajax');
        }
      });
    }
  });

  $("#include_for_upload").on("click", function(e){
    var tr = '#'+sales_id;
    include_for_upload(sales_id);
    $(tr).addClass('info');
    $("#modal_form").modal("hide");
    $("#save_resolve").prop("disabled", false);
  });

  $("#exclude_for_upload").on("click", function(e){
    var tr = '#'+sales_id;
    removeElement(sales_ids, sales_id); // footer.php
    if (sales_ids.length < 1) {
      $("#save_resolve").prop("disabled", true);
    }
    $(tr).removeClass('info');
    $("#modal_form").modal("hide");
  });

  $('#modal_form').on("hide", function(){
    $('.form-body').empty();
  });

  function include_for_upload(sales_id){
    var bool = find(sales_ids, sales_id); // footer.php
    if (!bool) {
      sales_ids.push(sales_id);
    }
  }

  $("#save_resolve").on("click", function(e){
    e.preventDefault();

    var agree = confirm('Are you sure?');
    if (agree && sales_ids.length > 0) {
      $.ajax({
        url: "<?php echo base_url(); ?>disapprove/save_resolve",
        type: "POST",
        data: { "sales_ids": sales_ids },
        success: function(data){
          location.reload();
        }
      });
    }
  });

</script>
