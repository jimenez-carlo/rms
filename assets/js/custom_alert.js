function error(data)
{
  var data = data || 'Error';
   try
   {
     alerty.alert(data,{title:'<i class="fa fa-exclamation-triangle" style="color:orange"></i> Error',okLabel: 'Ok'}); 
   }
   catch(err) 
   {
      alert(data);   
   }
 }
 function error_url(data,url){
  var data = data || 'Error';
   try
   {
     alerty.alert(data,
      {
        title: '<i class="fa fa-exclamation-triangle" style="color:orange"></i> Error',
        time:2222}, function(){ window.location = url; });
   }
   catch(err) 
   {
        alert(data);
     window.location.href = url;   
   }
 
 }
 function success(result){
 var result = result || 'Success!';
   try
   {
     alerty.toasts('<i class="fa fa-check"></i> '+result, {place: 'top', bgColor:'#00a65a',fontColor:'#fff'}); 
   }
   catch(err) 
   {
     alert(result);
   }
   
 }
  function success_refresh(result){
  var result = result || 'Success!';
   try
   {
     alerty.toasts(result+' <i class="fa fa-check"></i>', {place: 'top', bgColor:'#00a65a',fontColor:'#fff'},function (){
      if (window.location.href.indexOf("view") > -1) {
      location.reload(true);
    }else{
      window.location=window.location;
    }
    });
   }
   catch(err) 
   {
     alert(result);
   }
   
 }

 function success_without_refresh(result, form_name, recordid ){
 var result = result || 'Success!';
  try
  {
    result += ' <a href="'+ base_url +'record-list/view/'+ recordid +'/'+ post_js.has_access.form +'" style="color:yellow !important;">View ID#'+recordid+'</a>';
    alerty.toasts(result +' <i class="fa fa-check"></i>', {place: 'top', bgColor:'#00a65a',fontColor:'#fff'},function (){
     if (window.location.href.indexOf("view") > -1) {
     //location.reload(true);
    //  time:'99999';
     form_name[0].reset();
   }else{
     //window.location=window.location;
     $('#new_form')[0].reset();
   }
   });
  }
  catch(err) 
  {
    alert(result);
  }
  
}

function success_without_refresh_edit(result, form_name){
 var result = result || 'Success!';
  try
  {
    alerty.toasts(result+' <i class="fa fa-check"></i>', {place: 'top', bgColor:'#00a65a',fontColor:'#fff'},function (){
     if (window.location.href.indexOf("view") > -1) {
     //location.reload(true);
     form_name[0].reset();
   }else{
     //window.location=window.location;
     form_name[0].reset();
   }
   });
  }
  catch(err) 
  {
    alert(result);
  }
  
}

 function view_success(result,formid,id){
  var result = result || 'Success!';
  alerty.toasts(result+' <i class="fa fa-check"></i>', {place: 'top', bgColor:'#00a65a',fontColor:'#fff'},function (){});
// /,time:'9999'
  var f = document.createElement('form');
    f.action=base_url+"Form_Dynamic_Main/view/"+id;
    f.method='POST';
    //f.target='_blank';

    var i=document.createElement('input');
    i.type  ='hidden';
    i.name  ='form_id';
    i.value = formid;
    f.appendChild(i);

    document.body.appendChild(f);
    f.submit();
   
 }

 function view_success_wo_refresh(result,formid,id,modal){
  var result = result || 'Success!';
  alerty.toasts(result+' <i class="fa fa-check"></i>', {place: 'top', bgColor:'#00a65a',fontColor:'#fff'},function (){});
  $("#div_change_status").css("display", "none");
 }
 
 function confirmation(confirmation_title,confirmation_text,confirmation_button_text,exec_function,cancel_function){
  cancel_function =  cancel_function || function(){};
  try
   {
         alerty.confirm(
        confirmation_text, 
        {title: confirmation_title, cancelLabel: 'Cancel', okLabel: confirmation_button_text}, 
        function(){
          exec_function();
        },
        function() {
          cancel_function();//alerty.toasts('this is cancel callback')
        }
      )
   }
   catch(err) 
   {
       var r = confirm(confirmation_title,exec_function);
       if(r==true) 
       {
         exec_function();
       }      
   }

 }
 
 
