var engines = new Array();

$('#engine-no').on('keypress', function(e){
  if (e.keyCode === 13 && check_length($(this).val())) {
    get_engine($(this).val());
  }
});

$('#search-repo-in').on('click', function() {
  var engine_number = $('#engine-no').val();
  if (check_length(engine_number)) {
    get_engine(engine_number);
  }
});

function get_customer(cust_code) {
  $.ajax({
    url: BASE_URL+'repo/customer',
    type: "POST",
    data: {"cust_code" : cust_code},
    dataType: 'json',
    success: function(data) {
      if (!data.hasOwnProperty('error')) {
        if (data.log) {
          console.log(data.log);
        }
        $('#customer-id').val(data.cid);
        $('#first-name').val(data.first_name);
        $('#last-name').val(data.last_name);
      } else {
        return error_msg(data.error);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log([jqXHR, textStatus, errorThrown]);
      alert('Error get data from ajax');
    }
  });
}

function get_engine(engine_number) {
  var data = {"engine_no":engine_number};
  $.ajax({
    url: BASE_URL+'repo/get_sales',
    type: "POST",
    data: data,
    dataType: 'json',
    success: function(data) {
      if (!data.hasOwnProperty('error')) {
        if (data.log) {
          console.log(data.log);
        }
        $('#form-landing').empty().append(data.form);
        $('#cust-code').focus();
        //if (engines.indexOf(data.engine_no) === -1) {
        //  engines.push(data.engine_no);
        //  //var trow = '\
        //  //  <tr>\
        //  //    <td>'+data.customer_name+'</td>\
        //  //    <td>'+data.engine_no+'</td>\
        //  //    <td>'+data.mvf_no+'</td>\
        //  //    <td class="bname-'+data.eid+'">'+data.bname+'</td>\
        //  //    <td>\
        //  //      <input class="datepicker" type="text" name="regn-date-'+data.eid+'" value="'+data.registration_date+'" autocomplete="off" >\
        //  //    </td>\
        //  //    <td style="width: 130px">\
        //  //      <button class="btn btn-small btn-primary claim" style="width:85px;height:26px;margin-right:0.5rem" value="'+data.eid+'">\
        //  //        Claim this!\
        //  //      </button>\
        //  //      <button class="btn btn-small btn-danger" style="width:30px;" value="'+data.engine_no+'"><i class="icon-trash"></i></button>\
        //  //    </td>\
        //  //  </tr>';
        //  //$('#table_content').prepend(trow);
        //} else {
        //  return error_msg('Duplicate Engine Number.');
        //}
      } else {
        return error_msg(data.error);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log([jqXHR, textStatus, errorThrown]);
      alert('Error get data from ajax');
    }
  });
}

$(document).on('focusout', '#get-cust', function(e) {
  //if (e.keyCode === 13) {
  //}
  console.log(e);
  var cust_code = $(this).val();
  if (check_length(cust_code)) {
    get_customer(cust_code);
  }
});

$(document).on('click', '.btn-danger', function(e){
  e.preventDefault();
  $(this).closest('tr').remove();
  const index = engines.indexOf($(this).val());
  if (index > -1) {
    engines.splice(index, 1);
  }
});

$(document).on('click', '.claim', function(e){
  e.preventDefault();
  var engine_id = $(this).val()
  var td = $(this).parent();
  var confirmed = confirm('Are you sure?');
  if (confirmed) {
    e.preventDefault();
    $.ajax({
      url: BASE_URL+'repo/claim_repo',
      type: "POST",
      data: { eid: engine_id },
      dataType: 'json',
      success: function(data) {
        //console.log(data.log);
        td.children().remove();
        td.prepend('<span style="color:green">Success! <i class="icon-ok"></i><span>')
        $('.bname-'+engine_id).empty().append(data.branch);
      }
    });
  }
});

$(document).on('focusin', '.datepicker', function() {
  $(this).datepicker({ format: 'yyyy-mm-dd' });
});

function error_msg(error) {
  $('#error-msg').empty().append(error);
  $('.alert').show();
}

function check_length(value) {
  if (value.length > 5) {
    return true;
  }
}
