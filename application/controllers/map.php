<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->lan_auth->setLevel(1);
		
		if ($this->lan_auth->logged())  {

			$this->template->set_partial("login", "partials/loggedin",array("username" => $this->session->userdata('username')));
			$this->template->set_partial("navigation","partials/navigation", array(
				"map_active" => "active"
			));

		}

		if($this->session->userdata("type")==9) {

			$this->template->set_partial("adminlogo","partials/admin-logo");

		} else {

			$this->template->inject_partial("adminlogo","");
			
		}

		$this->msg->generate_msg();

	}

	public function index()
	{
		
		$this->template
			->title($this->lang->line("base_map"))
			->build("map");

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */