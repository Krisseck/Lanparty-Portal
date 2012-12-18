<?php

class Users_model extends CI_Model {

    	function __construct()
    	{
			parent::__construct();
		}
	
	function checkSeat($seat, $code) {
	
		$this->db->where("seat",$seat);
		$this->db->where("code",$code);
		$this->db->where("username","");

		if($this->db->count_all_results("users")>0) {
			return true;
		} else {
			return false;
		}
	
	}

	function insert($seat,$data) {

		$this->db->where("seat",$seat);
		$this->db->set($data);

		return $this->db->update("users");

	}

	function insertNewUser($data) {

		$this->db->set($data);

		return $this->db->insert("users");

	}

	function getUserBySeat($seat) {

		$this->db->where("seat",$seat);

		$q = $this->db->get("users");

		return $q->row_array();

	}

	function insertEmptySeat($seat,$code) {

		$this->db->set("seat",$seat);
		$this->db->set("code",$code);

		return $this->db->insert("users");

	}

	function getAll() {

		$q = $this->db->get("users");

		return $q->result_array();

	}
	
}
