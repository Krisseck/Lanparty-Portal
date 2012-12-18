<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->lan_auth->setLevel(9);

		$this->template->set_partial("login", "partials/loggedin",array("username" => $this->session->userdata('username')));
		$this->template->set_partial("adminlogo","partials/admin-logo");
		$this->template->set_partial("navigation","partials/navigation-admin", array(
			"dashboard_active" => "active"
		));

		$this->msg->generate_msg();

	}

	public function index()
	{
		
		$this->template
			->title($this->lang->line("base_dashboard"))
			->build("dashboard-admin");

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */