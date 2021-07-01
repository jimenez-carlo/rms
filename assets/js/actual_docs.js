$('.edit-actual-docs').on('click', function(e){
  e.preventDefault();
  $(this).hide();
  var id = $(this).val();
  $('#td-'+id).empty().append(
    '<input id="tn-'+id+'" class="input-medium" name="transmittal_number" type="text" placeholder="Input Transmittal Number" required>'
  );
  $('#save-'+$(this).val()).show();
});

$('.save-actual-docs').on('click', function(e){
  e.preventDefault();
  var actual_docs_id = $(this).closest('tr').prop('id');
  var temp_id = $(this).val();
  save_transmittal_no(temp_id, actual_docs_id);
});
$('[name=deposit_slip]').on('change', function(e){
  var that = $(this);
  var actual_docs_id = $(this).closest('tr').prop('id');
  var dep_slip_values = [
    "Original", "Not Original", 'No Deposit Slip',
    'Incomplete Miscellaneous OR', 'No Miscellaneous OR'
  ];

  var data_to_send = {
    'actual_docs_id': actual_docs_id,
    'deposit_slip': $(this).val()
  };

  if (dep_slip_values.indexOf(data_to_send.deposit_slip) !== -1) {
    var promise = data_send(data_to_send, BASE_URL+'actual_docs/update_status');
    promise.success(function(data){
      if (data.actual_doc.deposit_slip === 'Original') {
        //that.select2('disable');
        $('#date-complete-'+data.actual_doc.actual_docs_id).empty().append(data.actual_doc.date_completed);
      } else {
        $('#date-complete-'+data.actual_doc.actual_docs_id).empty().append(data.actual_doc.date_completed);
        $('#date-incomplete-'+data.actual_doc.actual_docs_id).empty().append(data.actual_doc.date_incomplete);
      }
      $('#status-'+data.actual_doc.actual_docs_id).empty().append(data.actual_doc.status);
      $('.alert').remove();
      $('#actual_docs').before(data.message);
    });
  }
});

function data_send(dataSend, url) {
  return $.ajax({
    url: url,
    data: dataSend,
    dataType: "json",
    type: "POST",
    beforeSend: function(){
      $(this).prop('disabled');
      $('.ajax-loader').show();
    },
    complete: function(){
      $('.ajax-loader').hide();
    }
  });
}

function save_transmittal_no(temp_id, actual_docs_id) {
  if ($('#tn-'+temp_id).val().length == 0) {
    confirm('Please input transmittal number.');
  } else {
    var confirmed = confirm('This action cannot be undone: Transmittal Number. Continue?');

    if (confirmed) {
      var voucher_or_electronic_payment_id = $('#id-'+temp_id).val();
      var transmittal_number = $('#tn-'+temp_id).val();
      var payment_method = $('#pt-'+temp_id).val();

      if(actual_docs_id.length === 0) {
        var actual_docs_id = null;
      }

      var dataToSend = {
        'actual_docs_id': actual_docs_id,
        'voucher_or_electronic_payment_id': voucher_or_electronic_payment_id,
        'transmittal_number': transmittal_number,
        'payment_method': payment_method,
      }

      $.ajax({
        url : BASE_URL+'actual_docs/save_transmittal_number',
        data: dataToSend,
        dataType: "json",
        type: "POST",
        beforeSend: function(){
          $('.ajax-loader').show();
          $('.alert').remove();
        },
        complete: function(){
          $('.ajax-loader, .save-actual-docs').hide();
        },
        success: function(data) {
          $('#actual_docs').before(data.message);
          $('#td-'+temp_id).empty().append(data.actual_docs.transmittal_number);
          $('.status-'+temp_id).empty().append(data.actual_docs.status);
        }
        //,error: function (jqXHR, textStatus, errorThrown){
        //}
      });
    }
  }

}
