var docid = function (id) { return document.getElementById(id); }
var docqa = function (id) { return document.querySelectorAll(id); }
var doca = function (id) { return document.querySelector(id); }

function disable_form(form) {
  var elements = form.elements;
  for (var i = 0, len = elements.length; i < len; ++i) {
    elements[i].disabled = true;
  }
}

// function req_ajax(controller, parameters, run_function, content) {
//   var xhr = new XMLHttpRequest();
//   if (content == true) {
//     xhr.onload = function () {
//       run_function();
//       docid(run_function().target).innerHTML = xhr.response;
//       run_function().event;
//     }
//   } else {
//     if (this.responseText != '') {
//       var res = JSON.parse(this.responseText);
//       if (res.type == 'success') {
//         run_function();
//         success(res.message);
//       } else {
//         error(res.message);
//       }
//     }else{
//       error("Bad Request");
//     }
//   }
//   xhr.open("POST", BASE_URL + controller, true);
//   xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//   xhr.send(parameters);
// }


// function submit_open_misc() {
//   this.disabled = true;
//   this.innerText = 'Updating...';
//   var form_submit = docid("FormModal");
//   var sub_params = new FormData(form_submit);
//   sub_params.append("attachment", file);
//   sub_params.append("action", 'resolve_repo_misc');
//   disable_form(form_submit);
//   req_ajax('Request', sub_params, function () {
//     var row = document.getElementById("tr_id_" + id);
//     row.parentNode.removeChild(row);
//     $('#modal-container').modal('toggle');
//   }, false);
// }

// function open_edit_misc() {
//   $('select').select2();
//   try { $(".datepicker").datepicker({ format: 'yyyy-mm-dd' }); } catch (error) { }

//   $('#modal-container').modal('toggle');

//   var obj = {};
//   obj.event = docid('FormModal').addEventListener("submit", 
//   function (evt) { 
//     evt.preventDefault();
//     confirmation('Please make sure all information are correct before proceeding.', 'Continue?', 'Ok',  function () { 
//       submit_open_misc(); 
//     }); 
//   });
//   obj.target = 'modal_body';
//   return obj;
// }

// var btn_edit_misc = docqa('.btn-edit-misc');
// btn_edit_misc.forEach((btn_edit_misc) => {
//   btn_edit_misc.addEventListener("click", (event) => {
//     event.preventDefault();
//     doca('.modal-title').innerText = event.target.dataset.title;
//     var id = event.target.value;
//     var params = "misc_id=" + id + "&action=edit_repo_misc";
//     req_ajax('Request', params, open_edit_misc, true);
//   });
// });

try {
  // CCN MISC
  var btn_edit_misc = docqa('.btn-edit-misc')
  btn_edit_misc.forEach((btn_edit_misc) => {
    btn_edit_misc.addEventListener("click", (event) => {
      doca('.modal-title').innerText = event.target.dataset.title;
      var id = event.target.value;
      var params = "misc_id=" + id + "&action=edit_repo_misc";
      var xhr = new XMLHttpRequest();
      xhr.onload = function () {
        docid('modal_body').innerHTML = xhr.response;
        $('select').select2();
        try { $(".datepicker").datepicker({ format: 'yyyy-mm-dd' }); } catch (error) { }
        $('#modal-container').modal('toggle');
        //Update Misc
        docid('FormModal').addEventListener("submit", function (evt) {
          evt.preventDefault();
          confirmation('Please make sure all information are correct before proceeding.', 'Continue?', 'Ok', function () {
            this.disabled = true;
            this.innerText = 'Updating...';
            var form_submit = docid("FormModal");
            var sub_params = new FormData(form_submit);
            sub_params.append("attachment", file);
            sub_params.append("action", 'resolve_repo_misc');
            disable_form(form_submit);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
              if (this.readyState == 4 && this.status == 200) {
                var res = JSON.parse(this.responseText);
                if (res != '') {
                  if (res.type == 'success') {
                    var row = document.getElementById("tr_id_" + id);
                    row.parentNode.removeChild(row);
                    $('#modal-container').modal('toggle');
                    success(res.message);
                  } else {
                    error(res.message);
                  }
                }

              }
            };
            xhr.open("POST", BASE_URL + 'Request', true);
            xhr.send(sub_params);
          });
        });

      }
      xhr.open("POST", BASE_URL + "Request", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(params);
    });
  });

} catch (error) {

}


try {
  // CCN SALES
  var btn_edit_sales = docqa('.btn-edit-sales')
  btn_edit_sales.forEach((btn_edit_sales) => {
    btn_edit_sales.addEventListener("click", (event) => {
      doca('.modal-title').innerText = event.target.dataset.title;
      var id = event.target.value;
      var params = "repo_sale_id=" + id + "&action=edit_repo_sales";
      var xhr = new XMLHttpRequest();
      xhr.onload = function () {
        docid('modal_body').innerHTML = xhr.response;
        $('select').select2();
        try { $(".datepicker").datepicker({ format: 'yyyy-mm-dd' }); } catch (error) { }
        $('#modal-container').modal('toggle');
        //Update Sale
        docid('FormModal').addEventListener("submit", function (evt) {
          evt.preventDefault();
          confirmation('Please make sure all information are correct before proceeding.', 'Continue?', 'Ok', function () {
            this.disabled = true;
            this.innerText = 'Resolving...';
            var form_submit = docid("FormModal");
            var sub_params = new FormData(form_submit);
            try { sub_params.append("reg_img", reg_img); } catch (error) { }
            try { sub_params.append("ren_img", ren_img); } catch (error) { }
            try { sub_params.append("reg_trans", reg_trans); } catch (error) { }
            try { sub_params.append("reg_pnp", reg_pnp); } catch (error) { }
            try { sub_params.append("reg_ins", reg_ins); } catch (error) { }
            try { sub_params.append("reg_em", reg_em); } catch (error) { }
            try { sub_params.append("reg_mac", reg_mac); } catch (error) { }
            sub_params.append("action", 'resolve_repo_sale');
            disable_form(form_submit);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
              if (this.readyState == 4 && this.status == 200) {
                var res = JSON.parse(this.responseText);
                if (res != '') {
                  if (res.type == 'success') {
                    var row = document.getElementById("tr_id_" + id);
                    row.parentNode.removeChild(row);
                    $('#modal-container').modal('toggle');
                    success(res.message);
                  } else {
                    error(res.message);
                  }
                }

              }
            };
            xhr.open("POST", BASE_URL + 'Request', true);
            xhr.send(sub_params);
          });
        });

      }
      xhr.open("POST", BASE_URL + "Request", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(params);
    });
  });

} catch (error) {

}

try {
  // CCN CREATE RETURN FUND
  var btn_add_return_fund = docqa('.btn-add-return-fund')
  btn_add_return_fund.forEach((btn_add_return_fund) => {
    btn_add_return_fund.addEventListener("click", (event) => {
      doca('.modal-title').innerText = event.target.dataset.title;
      var id = event.target.value;
      var params = "batch_id=" + id + "&action=create_repo_return_fund";
      var xhr = new XMLHttpRequest();
      xhr.onload = function () {
        docid('modal_body').innerHTML = xhr.response;
        $('select').select2();
        try { $(".datepicker").datepicker({ format: 'yyyy-mm-dd' }); } catch (error) { }
        $('#modal-container').modal('toggle');
        //Save Return Fund
        docid('FormModal').addEventListener("submit", function (evt) {
          evt.preventDefault();
          confirmation('Please make sure all information are correct before proceeding.', 'Continue?', 'Ok', function () {
            this.disabled = true;
            this.innerText = 'Creating Return Fund...';
            var form_submit = docid("FormModal");
            var sub_params = new FormData(form_submit);
            sub_params.append("attachment", attachment);
            sub_params.append("action", 'add_repo_return_fund');
            disable_form(form_submit);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
              if (this.readyState == 4 && this.status == 200) {
                var res = JSON.parse(this.responseText);
                if (res != '') {
                  if (res.type == 'success') {
                    $('#modal-container').modal('toggle');
                    success(res.message);
                  } else {
                    error(res.message);
                  }
                }
              }
            };
            xhr.open("POST", BASE_URL + 'Request', true);
            xhr.send(sub_params);
          });
        });
      }
      xhr.open("POST", BASE_URL + "Request", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(params);
    });
  });

} catch (error) {

}

try {
  // CCN EDIT RETURN FUND
  var btn_edit_repo_fund = docqa('.btn-edit-repo-fund')
  btn_edit_repo_fund.forEach((btn_edit_repo_fund) => {
    btn_edit_repo_fund.addEventListener("click", (event) => {
      doca('.modal-title').innerText = event.target.dataset.title;
      var id = event.target.value;
      var params = "return_fund_id=" + id + "&action=edit_repo_return_fund";
      var xhr = new XMLHttpRequest();
      xhr.onload = function () {
        docid('modal_body').innerHTML = xhr.response;
        $('select').select2();
        $(".un-select").select2("destroy");
        try { $(".datepicker").datepicker({ format: 'yyyy-mm-dd' }); } catch (error) { }
        $('#modal-container').modal('toggle');
        //Update Return Fund
        docid('FormModal').addEventListener("submit", function (evt) {
          evt.preventDefault();
          confirmation('Please make sure all information are correct before proceeding.', 'Continue?', 'Ok', function () {
            this.disabled = true;
            this.innerText = 'Updating Return Fund...';
            var form_submit = docid("FormModal");
            var sub_params = new FormData(form_submit);
            try { sub_params.append("attachment", attachment); } catch (error) { }
            sub_params.append("action", 'update_repo_return_fund');
            disable_form(form_submit);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
              if (this.readyState == 4 && this.status == 200) {
                var res = JSON.parse(this.responseText);
                if (res != '') {
                  if (res.type == 'success') {
                    var row = document.getElementById("tr_id_" + id);
                    row.parentNode.removeChild(row);
                    $('#modal-container').modal('toggle');
                    success(res.message);
                  } else {
                    error(res.message);
                  }
                } else {
                  $('#modal-container').modal('toggle');
                  error("Administrator has been alerted Due To User Activity!");
                }
              }
            };
            xhr.open("POST", BASE_URL + 'Request', true);
            xhr.send(sub_params);
          });
        });
      }
      xhr.open("POST", BASE_URL + "Request", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(params);
    });
  });

} catch (error) {

}


try {
  // ACCTG SALES
  var btn_view_sales = docqa('.btn-view-sales')
  btn_view_sales.forEach((btn_view_sales) => {
    btn_view_sales.addEventListener("click", (event) => {
      doca('.modal-title').innerText = event.target.dataset.title;
      var id = event.target.value;
      var params = "repo_sale_id=" + id + "&action=view_repo_sales";
      var xhr = new XMLHttpRequest();
      xhr.onload = function () {
        docid('modal_body').innerHTML = xhr.response;
        $(".un-select").select2("destroy");
        try { $(".datepicker").datepicker({ format: 'yyyy-mm-dd' }); } catch (error) { }
        $('#modal-container').modal('toggle');
        //Update Sale
        docid('FormModal').addEventListener("submit", function (evt) {
          evt.preventDefault();
          confirmation('Please make sure all information are correct before proceeding.', 'Continue?', 'Ok', function () {
            this.disabled = true;
            this.innerText = 'Resolving...';
            var form_submit = docid("FormModal");
            var sub_params = new FormData(form_submit);
            sub_params.append("action", 'reject_repo_sale');
            disable_form(form_submit);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
              if (this.readyState == 4 && this.status == 200) {
                var res = JSON.parse(this.responseText);
                if (res != '') {
                  if (res.type == 'success') {
                    var row = document.getElementById("tr_id_" + id);
                    row.parentNode.removeChild(row);
                    $('#modal-container').modal('toggle');
                    success(res.message);
                  } else {
                    error(res.message);
                  }
                }
              }
            };
            xhr.open("POST", BASE_URL + 'Request', true);
            xhr.send(sub_params);
          });
        });

      }
      xhr.open("POST", BASE_URL + "Request", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(params);
    });
  });

} catch (error) {

}

