<?php

class Settings_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	function get($key) {
	
		$this->db->where("key",$key);

		$q = $this->db->get("settings");

		$value = $q->row_array();

		$tempvalue = $value['value'];

		$check = unserialize($tempvalue);

		if($check===FALSE) {

			return $tempvalue;

		} else {

			return $check;

		}

	
	}

	function set($key, $value) {

		$this->db->where("key",$key);

		if($this->db->count_all_results("settings")>0) {

			$this->db->where("key",$key);

			$this->db->set("value",$value);

			return $this->db->update("settings");

		} else {

			$this->db->set("key",$key);

			$this->db->set("value",$value);

			return $this->db->insert("settings");

		}
		
	}
	
}
