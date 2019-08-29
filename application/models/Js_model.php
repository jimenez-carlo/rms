<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class Js_model extends CI_Model{

  public function jquery_checkall($id, $class) {

    $js = <<<JAVASCRIPT
      $('#{$id}').click(function(){
        if ($(this).is(':checked')) {
          $('.{$class}').prop('checked', true);
        } else {
          $('.{$class}').prop('checked', false);
        }
      })
JAVASCRIPT;

    return $js;

  }

}
