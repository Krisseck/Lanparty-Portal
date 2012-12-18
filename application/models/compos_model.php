<?php

class Compos_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	function getAll() {

		$this->db->order_by("weight");

		$q = $this->db->get("compos");

		return $q->result_array();

	}

	function get($id) {

		$this->db->where("id",$id);

		$q = $this->db->get("compos");

		return $q->row_array();

	}
	
}
