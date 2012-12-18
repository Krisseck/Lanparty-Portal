<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fill extends CI_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{

		$this->load->model("users_model");

		$users = $this->users_model->getAll();

		$tickets = "";

		foreach($users as $user) {

			$tickets .= $this->load->view("partials/ticket",$user,true);

		}

		$this->template->inject_partial("tickets",$tickets);
		
		$this->template
			->set_layout("fill")
			->build("fill");

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */