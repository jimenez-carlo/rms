function create_voucher(company_id, region_id) {
  var dataObj = { "company_id": company_id, "region_id": region_id }

  $.ajax({
    url : BASE_URL+"projected_fund/create_voucher",
    data: dataObj,
    type: "POST",
    success: function(data)
    {
      $(".error").html("");
      $('.form-body').html(data); // reset form on modals
      $("#modal_form").modal('show'); // show bootstrap modal
      loadjs();
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function loadjs() {
  $("input[name='voucher[voucher_no]']").on("keyup", function(e) {
    var vid = $(this).attr("data-id");
    var bool = ($(this).val().length > 3) ? false : true;
    $("#button-"+vid).prop("disabled", bool);
  });

  $(".save-voucher").on("click", function(e){
    e.preventDefault();
    if (confirm('Please make sure that all information are correct before proceeding. Continue?')) {
      var vid = $(this).val();
      var voucher_no = $("#voucher-"+vid).val();
      var data = {
        "vid": vid, "voucher_no": voucher_no
      }
      $.ajax({
        url : BASE_URL+"projected_fund/save_voucher",
        type: "POST",
        data: data,
        dataType: "json",
        success: function(data) {
          if(data.status) {
            $("#alert-status").removeClass("alert-error").addClass("alert-success");
            $("#voucher-"+vid+", #button-"+vid).prop("disabled", true);
          } else {
            $("#alert-status").removeClass("alert-success").addClass("alert-error");
          }
          $(".error").empty().append(data.message);
          $("#alert-status").show();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          alert("Error get data from ajax");
        }
      });
    }
  });
}

