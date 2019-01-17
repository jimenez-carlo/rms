<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
	
	public function __construct() { 
	    parent::__construct();
	}

	public function is_not($str, $field)
	{
		$this->CI->form_validation->set_message('is_not', "%s contains an invalid value.");
		return $str!==$field;
	}

	public function non_zero($str)
	{
		$this->CI->form_validation->set_message('non_zero', "%s cannot be zero.");
		return sprintf('%0.2f', $str)!=='0.00';
	}
}