<div class="container-fluid">
  <div class="row-fluid">
    <div class="block">
      <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo Tip Matrix</div>
      </div>
      <div class="block-content collapse in">
        <form class="form-horizontal" method="post" id="tip_form">
          <div class="row">
            <div class="form-actions" style="margin-left: 50px;padding-left:unset">
              <input type="button" class="btn btn-success" value="Add Branch" id="btn_add_tip" data-title="Add Branch Tip">
            </div>
            <div id="output" style="padding: 0px 2%;"></div>
          </div>
          <hr>
      </div>
    </div>
  </div>
</div>

<script>
  var BASE_URL = "<?php echo BASE_URL; ?>";
  var docid = function (id) { return document.getElementById(id); }
var docqa = function (id) { return document.querySelectorAll(id); }
var doca  = function (id) { return document.querySelector(id); }
  document.addEventListener("DOMContentLoaded", function(event) {
    load_tip_table();
  });

  document.getElementById('btn_add_tip').addEventListener('click', function() {
    doca('.modal-title').innerText = event.target.dataset.title;
    var params = "action=create_repo_branch_tip";
    var modal_create_tip = new XMLHttpRequest();
    modal_create_tip.onload = function() {
      docid('modal_body').innerHTML = modal_create_tip.response;
      $('select').select2();
      docid('FormModal').addEventListener("submit", function(evt) {
        evt.preventDefault();
        confirmation('Please make sure all information are correct before proceeding.', 'Continue?', 'Ok', function() {
          this.disabled = true;
          this.innerText = 'Resolving...';
          var form_submit = docid("FormModal");
          var elements = form_submit.elements;
          var sub_params = new FormData(form_submit);
          sub_params.append("action", 'save_repo_branch_tip');
          for (var i = 0, len = elements.length; i < len; ++i) {
            elements[i].disabled = true;
          }
          var xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              var res = JSON.parse(this.responseText);
              if (res != '') {
                if (res.type == 'success') {
                  $('#modal-container').modal('toggle');
                  success(res.message);
                  load_tip_table();
                } else {
                  error(res.message);
                }
              }

            }
          };
          xhr.open("POST", BASE_URL + 'Request', true);
          xhr.send(sub_params);
        });
      }, function() {
        error('Something Went Wrong Call Your Administrator For Assistance!');
      });

      $('#modal-container').modal('toggle');
    }
    modal_create_tip.open("POST", BASE_URL + "Request", true);
    modal_create_tip.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    modal_create_tip.send(params);
  });

  function load_tip_table() {
    var form_submit = document.getElementById("tip_form");
    var elements = form_submit.elements;
    var params = new FormData(form_submit);
    params.append("action", 'view_repo_matrix_table');
    var request_table = new XMLHttpRequest();
    request_table.onreadystatechange = function() {
      request_table.onload = function() {
        document.getElementById('output').innerHTML = request_table.response;
        $(function() {
          $(".table").dataTable({
            "sDom": "<\'row\'<\'span6\'l><\'span6\'f>r>t<\'row\'<\'span6\'i><\'span6\'p>>",
            "sPaginationType": "bootstrap",
            "oLanguage": {
              "sLengthMenu": "_MENU_ records per page"
            },
            "bSort": false,
            "iDisplayLength": 5,
            "aLengthMenu": [
              [5, 10, 25, 50, -1],
              [5, 10, 25, 50, "All"]
            ]
          });
        });
        var btn_edit_branch_tip = docqa('.btn-edit-branch-tip')
        btn_edit_branch_tip.forEach((btn_edit_branch_tip) => {
          btn_edit_branch_tip.addEventListener("click", (event) => {
            doca('.modal-title').innerText = event.target.dataset.title;
            var id = event.target.value;
            var edit_params = "branch=" + id + "&action=edit_repo_branch_tip";
            var request_edit = new XMLHttpRequest();
            request_edit.onload = function() {
              docid('modal_body').innerHTML = request_edit.response;
              $('#modal-container').modal('toggle');
              //Update Branch
              docid('FormModal').addEventListener("submit", function(evt) {
                evt.preventDefault();
                confirmation('Please make sure all information are correct before proceeding.', 'Continue?', 'Ok', function() {
                  $('select').select2();
                  this.disabled = true;
                  var form_submit = docid("FormModal");
                  var elements = form_submit.elements;
                  var update_params = new FormData(form_submit);
                  update_params.append("action", 'update_branch_tip');
                  for (var i = 0, len = elements.length; i < len; ++i) {
                    elements[i].disabled = true;
                  }
                  var request_update = new XMLHttpRequest();
                  request_update.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                      var res = JSON.parse(this.responseText);
                      if (res != '') {
                        if (res.type == 'success') {
                          $('#modal-container').modal('toggle');
                          success(res.message);
                          load_tip_table();
                        } else {
                          error(res.message);
                        }
                      }
                    }
                  };
                  request_update.open("POST", BASE_URL + 'Request', true);
                  request_update.send(update_params);
                });
              }, function() {
                error('Something Went Wrong Call Your Administrator For Assistance!');
              });

            }
            request_edit.open("POST", BASE_URL + "Request", true);
            request_edit.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request_edit.send(edit_params);
          });
        });
      }
    };
    request_table.open("POST", BASE_URL + 'Request', true);
    request_table.send(params);
  }
</script>