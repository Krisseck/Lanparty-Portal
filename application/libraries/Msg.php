<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Msg {

	var $CI;
	
	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	function addmsg($type,$msg) {
	
		$count = $this->CI->session->flashdata("msgcount");
		if($count=="") $count = 1; else $count++;
		
		$this->CI->session->set_flashdata("msg-".$count, $this->CI->template->load_view($type,array("message"=>$msg)));
		$this->CI->session->set_flashdata("msgcount", $count);
	
	}
	
	function generate_msg() {

		$msg = "";
		
		if($this->CI->session->flashdata("msgcount")) {

			for($i=1;$i<=$this->CI->session->flashdata("msgcount");$i++) {
			
				$msg .= $this->CI->session->flashdata("msg-".$i);
			
			}		
		
		}

		$this->CI->template->inject_partial("flashmsg",$msg);
	
	}
	
}
