var fid;

function nru(_fid)
{
  fid = _fid;
  $.ajax({
    url : "../rrt/nru/" + _fid,
    type: "POST",
    dataType: "JSON",
    success: function(data)
    {
      $(".error").html("");
      $(".alert-error").addClass("hide");
      $('#cash').html(data); // reset form on modals
      $('#modal_form_nru').modal('show'); // show bootstrap modal
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
      alert('Error get data from ajax');
    }
  });
}

function set_nru()
{
  $.ajax({
    url : "../rrt/set_nru/" + fid,
    type: "POST",
    data: $('#form_nru').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form_nru').modal('hide');
        location.reload();
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

function topsheet(_fid)
{
  fid = _fid;
  $(".error").html("");
  $(".alert-error").addClass("hide");

  $('input[name=batch_no]').val('').attr('type', 'text');
  $('input[name=batch_amount]').val('0.00');

  $('.batch-no').html('0.00').addClass('hide');
  $('.batch-amount').html('0.00');

  $('#btnBatch').removeClass('hide');
  $('#btnTopsheet').addClass('hide');

  $('#form_topsheet')[0].reset(); // reset form on modals
  $('#modal_form_topsheet').modal('show'); // show bootstrap modal
}

function get_batch()
{
  $.ajax({
    url : "../rrt/get_batch",
    type: "POST",
    data: $('#form_topsheet').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $(".error").html("");
        $(".alert-error").addClass("hide");

        $('input[name=batch_no]').attr('type', 'hidden');
        $('input[name=batch_amount]').val(data.amount);

        $('.batch-no').html(data.batch_no).removeClass('hide');
        $('.batch-amount').html(data.amount);

        $('#btnBatch').addClass('hide');
        $('#btnTopsheet').removeClass('hide');
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

function set_topsheet()
{
  $.ajax({
    url : "../rrt/set_topsheet/" + fid,
    type: "POST",
    data: $('#form_topsheet').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form_topsheet').modal('hide');
        location.reload();
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