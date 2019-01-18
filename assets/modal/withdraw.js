var fid;

function withdraw(_fid)
{
  fid = _fid;
  $(".error").html("");
  $(".alert-error").addClass("hide");
  $('#form_withdraw')[0].reset(); // reset form on modals
  $('#modal_form_withdraw').modal('show'); // show bootstrap modal
}

function save_withdraw()
{
  $("#form_withdraw input.numeric").each(function(){
    var val = toFloat($(this).val());
    $(this).val(val);
  });

  $.ajax({
    url : "fund/withdraw/" + fid + '/cash',
    type: "POST",
    data: $('#form_withdraw').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form_withdraw').modal('hide');
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

function check(_fid)
{
  fid = _fid;
  $(".error").html("");
  $(".alert-error").addClass("hide");
  $('#form_check')[0].reset(); // reset form on modals
  $('#modal_form_check').modal('show'); // show bootstrap modal
}

function save_check()
{
  $("#form_check input.numeric").each(function(){
    var val = toFloat($(this).val());
    $(this).val(val);
  });

  $.ajax({
    url : "fund/check/" + fid,
    type: "POST",
    data: $('#form_check').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form_check').modal('hide');
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

function set(_fid)
{
  fid = _fid;
  $(".error").html("");
  $(".alert-error").addClass("hide");
  $('#form_set')[0].reset(); // reset form on modals
  $('#modal_form_set').modal('show'); // show bootstrap modal
}

function save_set()
{
  $("#form_set input.numeric").each(function(){
    var val = toFloat($(this).val());
    $(this).val(val);
  });

  $.ajax({
    url : "fund/set/" + fid,
    type: "POST",
    data: $('#form_set').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form_set').modal('hide');
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

function set_ca(_fid)
{
  fid = _fid;
  $(".error").html("");
  $(".alert-error").addClass("hide");
  $('#form_old_rms')[0].reset(); // reset form on modals
  $('#modal_form_old_rms').modal('show'); // show bootstrap modal
}

function save_ca()
{
  $("#form_old_rms input.numeric").each(function(){
    var val = toFloat($(this).val());
    $(this).val(val);
  });

  $.ajax({
    url : "fund/set_ca/" + fid,
    type: "POST",
    data: $('#form_old_rms').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form_old_rms').modal('hide');
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

function deposit(_fid)
{
  fid = _fid;
  $(".error").html("");
  $(".alert-error").addClass("hide");
  $('#form_check')[0].reset(); // reset form on modals
  $('#modal_form_d').modal('show'); // show bootstrap modal
}

function save_deposit()
{
  $("#form_d input.numeric").each(function(){
    var val = toFloat($(this).val());
    $(this).val(val);
  });
  
  $.ajax({
    url : "fund/deposit/" + fid,
    type: "POST",
    data: $('#form_d').serialize(),
    dataType: "JSON",
    success: function(data)
    {
      if(data.status)
      {
        $('#modal_form_d').modal('hide');
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