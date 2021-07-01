var docid = function (id) { return document.getElementById(id); }
var docqa = function (id) { return document.querySelectorAll(id); }
var doca  = function (id) { return document.querySelector(id); }

try {
  // CCN MISC
  var btn_edit_misc = docqa('.btn-edit-misc')
  btn_edit_misc.forEach((btn_edit_misc) => {
    btn_edit_misc.addEventListener("click", (event) => {
      doca('.modal-title').innerText = event.target.dataset.title;
      var id = event.target.value;
      var params = "misc_id=" + id + "&type=edit_repo_misc";
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
            var elements = form_submit.elements;
            var sub_params = new FormData(form_submit);
            sub_params.append("attachment", file);
            for (var i = 0, len = elements.length; i < len; ++i) {
              elements[i].disabled = true;
            }
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
        } , function () {
          error('Something Went Wrong Call Your Administrator For Assistance!');
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
      var params = "repo_sale_id=" + id + "&type=edit_repo_sales";
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
            var elements = form_submit.elements;
            var sub_params = new FormData(form_submit);
            sub_params.append("attachment", file);
            for (var i = 0, len = elements.length; i < len; ++i) {
              elements[i].disabled = true;
            }
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
        } , function () {
          error('Something Went Wrong Call Your Administrator For Assistance!');
        });

      }
      xhr.open("POST", BASE_URL + "Request", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(params);
    });
  });

} catch (error) {

}
