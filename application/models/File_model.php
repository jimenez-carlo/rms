<?php
defined ('BASEPATH') OR exit('No direct script access allowed');

class File_model extends CI_Model{

	public $rms_dir;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('upload');
		$this->rms_dir = './rms_dir';
	}

	public function upload_single()
	{
		$config['allowed_types'] = 'jpg|jpeg';
		$config['upload_path'] = $this->rms_dir.'/temp/';
		$config['max_size'] = '1024';
		$this->upload->initialize($config);

		if ($this->upload->do_upload('scanFiles'))
		{
			$file = new Stdclass();
			$file->filename = $this->upload->data('file_name');
			$file->path = $this->rms_dir.'/temp/'.$file->filename;
			return $file;
		}
		else {
			$_SESSION['warning'][] = $this->upload->display_errors();
		}
	}

	public function upload_multiple()
	{
		$config['allowed_types'] = 'jpg|jpeg';
		$config['upload_path'] = $this->rms_dir.'/temp/';
		$config['max_size'] = '1024';
		$this->upload->initialize($config);

		$uploaded_files = array();
		foreach ($_FILES['scanFiles']['name'] as $key => $val)
		{
			$_FILES['post_file']['name'] = $_FILES['scanFiles']['name'][$key];
			$_FILES['post_file']['type'] = $_FILES['scanFiles']['type'][$key];
			$_FILES['post_file']['tmp_name'] = $_FILES['scanFiles']['tmp_name'][$key];
			$_FILES['post_file']['error'] = $_FILES['scanFiles']['error'][$key];
			$_FILES['post_file']['size'] = $_FILES['scanFiles']['size'][$key];

			if ($this->upload->do_upload('post_file'))
			{
				$file = new Stdclass();
				$file->filename = $this->upload->data('file_name');
				$file->path = $this->rms_dir.'/temp/'.$file->filename;
				$uploaded_files[] = $file;
			}
			else {
				$_SESSION['warning'][] = $this->upload->display_errors();
			}
		}
		return $uploaded_files;
	}

        /**
         * Use the $optional parameter to rename the file use only for single upload.
         * Ex: $optional = filename.jpg
         *
         * @return bool
         */
        public function upload($batch_name, $img_dir, $optional = NULL) {
          $upload_path = $this->rms_dir.$img_dir;
          if (!is_dir($upload_path)) {
            mkdir($upload_path, 0775, true);
          }

          $config['allowed_types'] = 'jpg|jpeg';
          $config['upload_path'] = $upload_path;
          $config['max_size'] = '1024';
          $this->upload->initialize($config);

          $uploaded_files = array();
          foreach ($_FILES[$batch_name]['name'] as $key => $val) {
            $_FILES['post_file']['name'] = (isset($optional)) ? $optional : $key.'_'.$val[0];
            $_FILES['post_file']['type'] = $_FILES[$batch_name]['type'][$key][0];
            $_FILES['post_file']['tmp_name'] = $_FILES[$batch_name]['tmp_name'][$key][0];
            $_FILES['post_file']['error'] = $_FILES[$batch_name]['error'][$key][0];
            $_FILES['post_file']['size'] = $_FILES[$batch_name]['size'][$key][0];

            if (!$this->upload->do_upload('post_file')) {
	      $dir_files = directory_map($upload_path, 1);
              foreach ($dir_files as $file) {
                unlink($upload_path.'/'.$file);
              }
              $_SESSION['warning'][] = $this->upload->display_errors();
              return false;
            }
          }
          return true;
        }

	public function delete($file)
	{
		unlink($this->rms_dir.$file);
	}

	public function save_scan_docs($sales, $files, $temp)
	{
		// create folder
		$folder = $this->rms_dir.'/scan_docs/'.$sales->sid.'_'.$sales->engine_no.'/';
		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}

		// get dir files
		$this->load->helper('directory');
		$dir_files = directory_map($folder, 1);

		// delete dir files
		foreach ($dir_files as $file) {
			if (!in_array($file, $files)) unlink($folder.$file);
		}

		// save temp
		foreach ($temp as $file) {
			if (!empty($file)) rename($this->rms_dir.'/temp/'.$file, $folder.$file);
		}
	}

	public function save_misc_scans2($misc, $files, $temp)
	{
		// create folder
		$folder = $this->rms_dir.'/misc/'.$misc->mid.'/';
		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}

		// get dir files
		$this->load->helper('directory');
		$dir_files = directory_map($folder, 1);

		// delete dir files
		foreach ($dir_files as $file) {
			if (!in_array($file, $files)) unlink($folder.$file);
		}

		// save temp
		foreach ($temp as $file) {
			if (!empty($file)) rename($this->rms_dir.'/temp/'.$file, $folder.$file);
		}

		return $file;
	}

	public function save_misc_scans($topsheet, $files, $temp)
	{
		// create folder
		$folder = $this->rms_dir.'/misc/'.$topsheet->tid.'_'.$topsheet->trans_no.'/';
		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}

		// get dir files
		$this->load->helper('directory');
		$dir_files = directory_map($folder, 1);

		// delete dir files
		foreach ($dir_files as $file) {
			if (!in_array($file, $files)) unlink($folder.$file);
		}

		// save temp
		foreach ($temp as $file) {
			if (!empty($file)) rename($this->rms_dir.'/temp/'.$file, $folder.$file);
		}
	}
}
