<?php

class Songs_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	function insert($title, $artist, $album, $url) {

		$this->db->set("title",$title);
		$this->db->set("artist",$artist);
		$this->db->set("album",$album);
		$this->db->set("url",$url);

		return $this->db->insert("songs");

	}

	function getTop() {

		$this->db->select("*, COUNT(*) as count",false);
		$this->db->group_by("title, artist, album, url");
		$this->db->order_by("count DESC, artist ASC, title ASC, album ASC, url ASC");

		$q = $this->db->get("songs");

		return $q->result_array();

	}
	
}
