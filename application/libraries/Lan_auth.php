<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lan_auth
{
	var $CI;
	var	$controller;
	var	$user_id;
	var	$user_type;
	var	$user_name;
	
	function Lan_auth()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->user_id = $this->CI->session->userdata('id');
		$this->user_name = $this->CI->session->userdata('username');
		$this->user_type = $this->CI->session->userdata("type");
	}
	
	function logged()
	{
		if(isset($this->user_id) && is_numeric($this->user_id) && $this->user_id > 0)
			return true;
		else
			return false;
	}
	
	function setLevel($minimum = 0)
	{
		
		if(!is_numeric($this->user_type))
		{
			//	EI KIRJAUTUNEENA
			
			// Siirretään flashdata login-sivulle
			
			if($this->CI->session->flashdata("msgcount")) {
					
				for($i=1;$i<=$this->CI->session->flashdata("msgcount");$i++) {
				
					$this->CI->session->set_flashdata("msg-".$i,$this->CI->session->flashdata("msg-".$i));
				
				}
				
				$this->CI->session->set_flashdata("msgcount",$this->CI->session->flashdata("msgcount"));
										
			}
			
			redirect('login');
		}
		else if($this->user_type < $minimum)
		{
			// 	TASO EI RIITÄ
			redirect("dashboard");
		}
	}
	
	function logIn($user_name = '', $user_password = '')
	{
		$logindb = $this->CI->load->database('default', TRUE);
		$logindb->where('username', $user_name);

		$result = $logindb->get('users');

		if($result->num_rows() == 1)
		{
			// Nimi Ok, tarkistetaan salasana
			
			$result = $result->row_array();

			$this->CI->load->library('hash');

			if (Hash::CheckPassword($user_password, $result['password']) !== FALSE)
			{
				
				//All ok								
				$this->CI->session->set_userdata($result);
			
				return true;
							
			}
			else
			{
				return false;
			}
		}
		else
		{
		
			// Kirjautuminen epäonnistui
			return false;
		}
	}

	function createPassword($password) {

		$this->CI->load->library('hash');

		return Hash::HashPassword($password);

	}
		
}
