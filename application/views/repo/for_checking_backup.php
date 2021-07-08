<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style type="text/css">
  .bld{
    font-weight: bold;
    text-align: right!important;
    font-size: 16px;
  }
  .brdrt{
    border-top: dotted gray!important;;
  }
  .brdrb{
    border-top: dotted gray!important;;
  }
  .clr-rd{
    color: red;
  }
  .info{
    background-color: #d9edf7!important;
  }
  .al{
    text-align: left!important;
  }
  thead>tr{
     border-top: double;
  }
  th:last-child,td:last-child{
    width: 0.1%;
    white-space: nowrap;
  }
</style>
<div class="container-fluid">
  <div class="row-fluid">
    <!-- block -->
    <div class="block span2">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Reference Number</div>
      </div>
      <div class="block-content collapse in">
        <ul style="list-style:none; margin:0; line-height:10px;">
          <?php foreach($references AS $x): ?>
          <li class="ref-list" style="line-height:28px; border-bottom:solid #ddd 1px;">
            <?php echo "<button class='btn btn-success btn-mini ca-ref' value='{$x['repo_batch_id']}'><i class='icon-edit'></i></button> {$x['reference']}"; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- block -->
    <div class="block span9" style="margin-left: 10px;">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Transaction <?php echo (isset($ca_ref)) ? '# '.$ca_ref['reference'] : ''; ?></div>
      </div>
      <div id="table-landing" class="block-content collapse in">
        <?php
        if(isset($view)) {
          print $view;
        }
        else {
          print '<p><span class="icon icon-chevron-left"></span> Select a CA reference to check OR CR attachment and details.</p>';
        }
        ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="modal-view" class="modal modal-wider hide fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3>View attachment</h3>
  </div>
  <div class="modal-body row">
  </div>
  <div class="modal-footer">
    <button id="include-for-upload" class="btn btn-success" data-dismiss="modal" aria-hidden="true" data-type="0">Include For Upload</button>
    <button id="save-da" class="btn btn-warning" disabled>Disapprove</button>
  </div>
</div>

<script>
var for_sap_upload = {
  "repo_registration_ids":[]
}

$("button.ca-ref").on("click", function() {
  var button = $(this)
  var repo_batch_id = button.val()
  var ajax = ajaxSend({ "repo_batch_id": repo_batch_id, "request_type": "CA_REF_DATA" })

  ajax.success(function(data, textStatus, jqXHR) {
    $("#table-landing").empty().append(data.table + '<button id="preview-summary" class="btn btn-success" name="preview" value="'+repo_batch_id+'"" disabled>Preview Summary</button>')
  })

  ajax.complete(function(jqXHR, textStatus) {
    $("li.ref-list").css("background-color", "#fff")
    $(".ca-ref").attr("disabled", false)
    button.parent("li").css("background-color", "#66cdaa")
    button.prop("disabled", true)
    viewAttachment(repo_batch_id)
  })
})
  var miscs = [];
  var sales = [];
$(document).on("click","#preview-summary",function() {
  $('[data-selectable]').each(function( index ) {
  if($( this ).is(':checked') ){
    if (isNaN($(this).val())) {
      miscs.push($(this).val());
    }else{
      sales.push($(this).val());
    }
  }
  });
  var misc_comma = miscs.join();
  var sale_comma = sales.join();
  console.table(misc_comma);
  console.table(sale_comma);
  var button = $(this)
  var repo_batch_id = button.val()
  var ajax = ajaxSend({ "repo_batch_id": repo_batch_id, "request_type": "CA_REF_DATA_PREV", })

  ajax.success(function(data, textStatus, jqXHR) {
    $("#table-landing").empty().append(data.table + '<input type="submit" name="submit_all" value="Submit" class="btn btn-success"> \
          <input type="submit" name="back" value="Back" class="btn btn-success">')
  })

  ajax.complete(function(jqXHR, textStatus) {
    $("li.ref-list").css("background-color", "#fff")
    $(".ca-ref").attr("disabled", false)
    button.parent("li").css("background-color", "#66cdaa")
    button.prop("disabled", true)
    viewAttachment(repo_batch_id)
  })
})

function includeForUpload(repo_registration_id) {
  if(for_sap_upload.repo_registration_ids.indexOf(repo_registration_id) === -1) {
    for_sap_upload.repo_registration_ids.push(repo_registration_id)
  }
  bool = (for_sap_upload.repo_registration_ids.length < 0)
  $("#preview-summary").prop("disabled", bool)
//   console.log(bool);
//   console.log(for_sap_upload.repo_registration_ids);
}

function viewAttachment(repo_batch_id) {
  $("button.view").on("click", function() {
    var button = $(this)
    var attach_type = button.attr("name")

    switch (attach_type) {
      case 'REPO_UNIT':
        var repo_registration_id = button.val()
        var ajax = ajaxSend({"request_type": "VIEW_ATTACHMENT" , "attachment": {  "repo_registration_id": repo_registration_id, "type": attach_type }})
        $('#include-for-upload').val(repo_registration_id)
        $('#include-for-upload').attr('data-type', 'sales');
        $('#save-da').val(repo_registration_id)
        $("#save-da").attr("disabled", true)
        ajax.success(function(data, textStatus, jqXHR) {
          $(".modal-body").empty().append(' \
            <div class="row form-horizontal"> \
              <div class="offset1 span3"> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Branch</label> \
                  <div class="controls" style="padding-top:5px;">'+data.branch+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Customer</label> \
                  <div class="controls" style="padding-top:5px;">'+data.customer_name+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Engine#</label> \
                  <div class="controls" style="padding-top:5px;">'+data.engine_no+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">RSF#</label> \
                  <div class="controls" style="padding-top:5px;">'+data.rsf_num+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">AR#</label> \
                  <div class="controls" style="padding-top:5px;">'+data.ar_num+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">AR Amt</label> \
                  <div class="controls" style="padding-top:5px;">'+data.ar_amt+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">OR/CR Amt</label> \
                  <div class="controls" style="padding-top:5px;">'+data.orcr_amt+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Renewal Amt</label> \
                  <div class="controls" style="padding-top:5px;">'+data.renewal_amt+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Transfer Amt</label> \
                  <div class="controls" style="padding-top:5px;">'+data.transfer_amt+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">HPG/PNP Clearance Amt</label> \
                  <div class="controls" style="padding-top:5px;">'+data.hpg_pnp_clearance_amt+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Insurance Amt</label> \
                  <div class="controls" style="padding-top:5px;">'+data.insurance_amt+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Emission Amt</label> \
                  <div class="controls" style="padding-top:5px;">'+data.emission_amt+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Macro Etching Amt</label> \
                  <div class="controls" style="padding-top:5px;">'+data.macro_etching_amt+'</div> \
                </div> \
              </div> \
              <div class="span3"> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Renewal Tip</label> \
                  <div class="controls" style="padding-top:5px;">'+data.renewal_tip+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Transfer Tip</label> \
                  <div class="controls" style="padding-top:5px;">'+data.transfer_tip+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">HPG/PNP Clearance Tip</label> \
                  <div class="controls" style="padding-top:5px;">'+data.hpg_pnp_clearance_tip+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Macro Etching Tip</label> \
                  <div class="controls" style="padding-top:5px;">'+data.macro_etching_tip+'</div> \
                </div> \
                <div class="control-group" style="margin-bottom:0"> \
                  <label class="control-label">Plate Tip</label> \
                  <div class="controls" style="padding-top:5px;">'+data.plate_tip+'</div> \
                </div> \
                <div style="height:auto;margin-top:100px;border:black solid 1px;border-radius:5px;padding:10px 10px 10px 10px;">  \
                  <label for="da"><bold>Disapprove </bold><?php echo preg_replace("/\r|\n/", "", form_checkbox(["type"=>"checkbox","id"=>"da"])); ?></label> \
                  <div id="da-reason" class="hide"> \
                    <label>Reason:</label> \
                    <div><?php echo preg_replace("/\r|\n/", "", form_dropdown('da_reason', ['Wrong Encode'=>'Wrong Encode', 'Invalid Format'=>'Invalid Format'])); ?></div> \
                  </div> \
                </div> \
              </div> \
              <div class="span5"> \
                <ul id="imgTab" class="nav nav-tabs"> \
                  <li class="active"><a href="#orcr-img">OR/CR</a></li> \
                  <li><a href="#renewal-img">Renewal</a></li> \
                  <li><a href="#transfer-img">Transfer</a></li> \
                  <li><a href="#clearance-img">HPG/PNP Clearance</a></li> \
                  <li><a href="#insurance-img">Insurance</a></li> \
                  <li><a href="#emission-img">Emission</a></li> \
                  <li><a href="#macro-etching-img">Macro Etching</a></li> \
                </ul> \
                <div class="tab-content"> \
                  <div class="tab-pane active" id="orcr-img"> \
                    <div class="control-group"> \
                      <label class="control-label">Registration OR/CR</label> \
                      <div class="controls"> \
                        <img src="'+BASE_URL+data.attachment.registration_orcr_img+'" alt="OR/CR"/> \
                      </div> \
                    </div> \
                  </div> \
                  <div class="tab-pane" id="renewal-img"> \
                    <div class="control-group"> \
                      <label class="control-label">Renewal OR</label> \
                      <div class="controls"> \
                        <img src="'+BASE_URL+data.attachment.renewal_or_img+'" alt="Renewal OR"/> \
                      </div> \
                    </div> \
                  </div> \
                  <div class="tab-pane" id="transfer-img"> \
                    <div class="control-group"> \
                      <label class="control-label">Transfer OR</label> \
                      <div class="controls"> \
                        <img src="'+BASE_URL+data.attachment.transfer_or_img+'" alt="Transfer OR"/> \
                      </div> \
                    </div> \
                  </div> \
                  <div class="tab-pane" id="clearance-img"> \
                    <div class="control-group"> \
                      <label class="control-label">HPG/PNP Clearance OR</label> \
                      <div class="controls"> \
                        <img src="'+BASE_URL+data.attachment.hpg_pnp_clearance_or_img+'" alt="HPG/PNP Clearance"/> \
                      </div> \
                    </div> \
                  </div> \
                  <div class="tab-pane" id="insurance-img"> \
                    <div class="control-group"> \
                      <label class="control-label">Insurance OR</label> \
                      <div class="controls"> \
                        <img src="'+BASE_URL+data.attachment.insurance_or_img+'" alt="Insurance OR"/> \
                      </div> \
                    </div> \
                  </div> \
                  <div class="tab-pane" id="emission-img"> \
                    <div class="control-group"> \
                      <label class="control-label">Emission OR</label> \
                      <div class="controls"> \
                        <img src="'+BASE_URL+data.attachment.emission_or_img+'" alt="Emission OR"/> \
                      </div> \
                    </div> \
                  </div> \
                  <div class="tab-pane" id="macro-etching-img"> \
                    <div class="control-group"> \
                      <label class="control-label">Macro Etching OR</label> \
                      <div class="controls"> \
                        <img src="'+BASE_URL+data.attachment.macro_etching_or_img+'" alt="Macro Etching"/> \
                      </div> \
                    </div> \
                  </div> \
                </div> \
              </div> \
            </div> \
          ')
        })
        break;
      case 'MISC_EXP':
        var misc_exp_id = button.val()
        var ajax = ajaxSend({"request_type": "VIEW_ATTACHMENT" , "attachment": {  "repo_batch_id": repo_batch_id, "misc_expense_id": misc_exp_id, "type": attach_type}})
        $("#save-da").attr("disabled", false)
        $('#save-da').val(misc_exp_id);
        $('#include-for-upload').attr('data-type', 'misc');
        $('#include-for-upload').val(misc_exp_id)
        ajax.success(function(data, textStatus, jqXHR) {
          $(".modal-body").empty().append(' \
            <div class="span5 offset1 form-horizontal"> \
              <div class="control-group"> \
                <label class="control-label">OR Date</label> \
                <div class="controls" style="padding-top:5px">'+data.or_date+'</div> \
              </div> \
              <div class="control-group"> \
                <label class="control-label">OR No.</label> \
                <div class="controls" style="padding-top:5px">'+data.or_no+'</div> \
              </div> \
              <div class="control-group"> \
                <label class="control-label">Expense Type</label> \
                <div class="controls" style="padding-top:5px">'+data.type+'</div> \
              </div> \
              <div class="control-group"> \
                <label class="control-label">Status</label> \
                <div class="controls" style="padding-top:5px">'+data.status_name+'</div> \
              </div> \
              <div class="control-group"> \
                <label class="control-label">Amount</label> \
                <div class="controls" style="padding-top:5px">'+data.amount+'</div> \
              </div> \
              <div id="disapproved_div" style="display:none"><div class="control-group"> \
                <label class="control-label">Reason</label> \
                <div class="controls" style="padding-top:5px">\
                <select name="misc_da_reason" id="misc_da_reason">\
                <?php foreach ($misc_da_dropdown as $res) {
                    echo '<option value="'.$res['status_id'].'"> '.$res['status_name'].'</option>\ '; 
                  } ?>
                </select>\
                </div> \
              </div> \
              <div class="control-group"> \
                <label class="control-label">Remarks</label> \
                <div class="controls" style="padding-top:5px">\
                <textarea name="remarks"></textarea>\
                </div> \
              </div> \
              <div class="control-group"> \
               <label class="control-label"></label> \
                <div class="controls" style="padding-top:5px">\
                <button class="btn btn-success btn-save">Save</button>  <button class="btn btn-success cncl">Cancel</button>\
                </div> \
              </div></div> \
            </div> \
            <div class="span6"> \
              <img src="'+BASE_URL+data.image_path+'" > \
            </div> \
          ')
        })
        break;
    }

    ajax.complete(function() {
      $("#modal-view").modal('show')
    })
  })
}

function ajaxSend(obj) {
  return $.ajax({
    type: "POST",
    dataType: "json",
    data: obj,
    error: function (jqXHR, textStatus, errorThrown) {
      // error callback
    }
  })
}

$("#modal-view").on("hide", function() {
  $(".modal-body").empty()
  $("#include-for-upload").prop("disabled", false)
  $("#save-da").prop("disabled", true)
})

$("#modal-view").on("show", function() {
  $('#imgTab a').click(function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

  $("#da").on("change", function () {
    var isDaChecked = $(this).is(':checked')
    if (isDaChecked) {
      $("#da-reason").show()
    } else {
      $("#da-reason").hide()
    }
    $("#include-for-upload").prop("disabled", isDaChecked)
    $("#save-da").prop("disabled", !isDaChecked)
  })

  $("#include-for-upload").on("click", function() {
     event.preventDefault();
    // includeForUpload($(this).val())
    // $("#"+$(this).attr('data-type')+"_id-"+$(this).val())
     if(!$("#"+$(this).attr('data-type')+"_id-"+$(this).val()).is(':checked')) {
      $("#"+$(this).attr('data-type')+"_id-"+$(this).val()).prop("checked", true)
    }
    // $("#"+$(this).val()).prop("checked", true)
  })

})

 $(document).on("click","#save-da",function() {
    if ($('#misc_da_reason').length > 0) {
      document.getElementById('disapproved_div').style.display = 'block';
    }else{
      var isConfirm = confirm("Please make sure all information are correct before proceeding. Continue?")
      if (isConfirm) {
        var element = document.getElementById("cb-"+$this); 
        element.parentNode.removeChild(element);
        var x = $("select[name='da_reason']").val()
        var ajax = ajaxSend({"disaprove":1})
        ajax.success(function() {
        })
      }
    }
  });
  $(document).on("click",".btn-save",function() {
      var isConfirm = confirm("Please make sure all information are correct before proceeding. Continue?")
      if (isConfirm) {
        var x = $("select[name='da_reason']").val()
        var ajax = ajaxSend({"disaprove":1})
        ajax.success(function() {
        })
      }
  });
 
  $(document).on("click",".cncl",function() {
      document.getElementById('disapproved_div').style.display = 'none';
  });
// include-for-upload
 
// $("#save-da").on("click", function(e) {
//   e.preventDefault()
  // var isConfirm = confirm("Are you sure?")
  // if (isConfirm) {
  //   var x = $("select[name='da_reason']").val()
  //   var ajax = ajaxSend({"disaprove":1})
  //   ajax.success(function() {

  //   })
  // }

// })
var expenses = 0;
$(document).on("click","input:checkbox",function() {
  if ($(this).data('amt')) { 
   var amt     = parseFloat($(this).data('amt'));
   var current = parseFloat($('.exp_display').html().replace(/₱ /g,'').replace(/\,/g,''));
   var bal     = parseFloat($('.bal').html().replace(/₱ /g,'').replace(/\,/g,''));
   console.log();
    if($(this).is(':checked')) {
      $('.exp_display').html('&#8369; '+( format_value(amt+current)) );
      $('.bal').html('&#8369; '+( format_value(bal-amt)) );
      expenses = expenses + amt;
        // $(this).closest('tr').removeClass('info');
    }else {
      $('.exp_display').html('&#8369; '+( format_value(current-amt)) ) ;
      $('.bal').html('&#8369; '+( format_value(bal+amt)) );
      expenses = expenses - amt;
        // $(this).closest('tr').addClass('info');
    }
    if(expenses > 0){
      $("#preview-summary").attr("disabled", false);
    }else{
      $("#preview-summary").attr("disabled", true);
    }
  }
});

function format_value(x) {
    var  n = x.toFixed(2);
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return x;
}
</script>
