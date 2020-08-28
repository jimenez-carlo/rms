<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row-fluid">
  <!-- block -->
  <div class="block">
    <div class="alert alert-error hide">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <!-- <strong>Not fund!</strong> --> <p id='error-msg'></p>
    </div>
    <div class="navbar navbar-inner block-header">
        <div class="pull-left">Repo In</div>
    </div>
    <div class="block-content collapse in">
      <label>Input Engine Number</label>
      <div class="form-inline">
        <input id="engine-no" type="text" value="">
        <button id="search-repo-in" class="btn btn-success" type="submit">Search</button>
      </div>
    </div>
  </div>
  <div id="form-landing" class="row"></div>
</div>
