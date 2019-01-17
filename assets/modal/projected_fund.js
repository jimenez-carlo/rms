var fid;

function create_voucher(_fid)
{
  fid = _fid;

  $.ajax({
    url : "projected_fund/create_voucher/" + fid,
    type: "POST",
    dataType: "JSON",
    success: function(data)
    {
      $(".error").html("");
      $(".alert-error").addClass("hide");
      $('.form-body').html(data); // reset form on modals
      $('#modal_form').modal('show'); // show bootstrap modal
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function save_voucher()
{
  if (confirm('Please make sure that all information are correct before proceeding. Continue?'))
  {
    $.ajax({
      url : "projected_fund/save_voucher/" + fid,
      type: "POST",
      data: $('#form').serialize(),
      dataType: "JSON",
      success: function(data)
      {
        if(data.status)
        {
          $('#modal_form').modal('hide');
          location.href='projected_fund/voucher';
        }
        else
        {
          $(".alert-error").removeClass("hide");
          $(".error").html("");
          $(".error").append(data.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        alert('Error get data from ajax');
      }
    });
  }
}

function total()
{
  var total = 0;

  $('#form .amount:checked').each(function(){
    total += parseFloat($(this).val());
  });

  $('#form #total-projected').text(commafy(total));

  total = parseFloat(total).toFixed(2);
  $('#form input[name=amount]').val(total);
}

function get_offline()
{
  if($('input[name=offline]').is(':checked')){
    $('.control-group.date').removeClass('hide');
  }
  else {
    $('.control-group.date').addClass('hide');

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1;
    var yyyy = today.getFullYear();

    if (dd < 10) dd = "0"+dd;
    if (mm < 10) mm = "0"+mm;
    $('input[name=date]').val(yyyy + '-' + mm + '-' + dd);
  }
}

function print()
{
  if ($("table.projected :checked").length > 0)
  {
    $("form#print").html(''); // clear form

    $("table.projected :checked").each(function(){
      $("form#print").append('<input type="hidden" name="'+this.name+'" value="'+this.value+'">');
    });

    $("form#print").submit(); // submit form 
  }
  else
  {
    $(".alert-error").removeClass("hide");
    $(".error").html("");
    $(".error").append("Please select at least one Projected Cost.");
  }
}