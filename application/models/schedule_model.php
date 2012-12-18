<?php

class Schedule_model extends CI_Model {

    	function __construct()
    	{
			parent::__construct();
		}
	
	function getAll() {

		$this->db->order_by("start");

		$q = $this->db->get("schedule");

		$schedule = array();

		foreach($q->result_array() as $event) {

			$schedule[date("Y-m-d",strtotime($event['start']))][date("H:i",strtotime($event['start']))] = $event;

		}

		return $schedule;

	}

	function get($id) {

		$this->db->where("id",$id);

		$q = $this->db->get("schedule");

		return $q->row_array();

	}
	
}
