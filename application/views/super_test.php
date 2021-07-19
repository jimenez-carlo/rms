<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <br><br>
  <button type="button" id="btn_load_btl">Load Table</button>
  
<form method="post" id="table_form" onsubmit="src(event)">
  <div id="content_here">
 
  </div>
</form>
</body>
<script>
    document.getElementById('btn_load_btl').addEventListener("click", function (event) {
      // doca('.modal-title').innerText = event.target.dataset.title;
      var id = event.target.value;
      // var params = "misc_id=" + id + "&action=edit_repo_misc";
      var xhr = new XMLHttpRequest();
      xhr.onload = function () {
        document.getElementById('content_here').innerHTML = xhr.response;
        var form_submit = document.getElementById("table_form");
      }
      xhr.open("POST", BASE_URL + "Request/table_api", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send();
    });
    function src(event){
      event.preventDefault();
            var params = new FormData(document.getElementById('table_form'));
            console.log(params);
            
            params.append(event.submitter.name, event.submitter.value);
            var sub_xhr = new XMLHttpRequest();
            sub_xhr.onreadystatechange = function () {
              if (this.readyState == 4 && this.status == 200) {
                document.getElementById('content_here').innerHTML = sub_xhr.response;
              }
            };
            sub_xhr.open("POST", BASE_URL+'Request/table_api', false);
            sub_xhr.send(params);
        }

</script>
</html>