<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		
		if ($this->lan_auth->logged())  {

			$this->template->set_partial("login", "partials/loggedin",array("username" => $this->session->userdata('username')));

		} else {

			$this->template->set_partial("login", "partials/loggedout");

		}

		$this->template->inject_partial("navigation","");
		
		if($this->session->userdata("type")==9) {

			$this->template->set_partial("adminlogo","partials/admin-logo");

		} else {

			$this->template->inject_partial("adminlogo","");
			
		}

		$this->msg->generate_msg();

	}

	function index()
	{

		$this->load->library("Hash");

		if($this->input->post("login-button")!="")
		{

			if($this->lan_auth->logIn($this->input->post('username'), $this->input->post('password')))
			{
				redirect('dashboard');
			}
			else 
			{
				$this->msg->addmsg("partials/alert",$this->lang->line("base_wrong_username_password"));
				redirect("login");
			}
		}
		
		$this->template
			->title($this->lang->line("base_login"))
			->build("login");

	}

	function logout()
	{
		$this->session->sess_destroy();
		redirect("/");
	}

	function register() {

		if($this->input->post("register-button")!="") {

			$this->load->library('form_validation');

			$this->form_validation->set_rules('code', $this->lang->line("base_code"), 'required');
			$this->form_validation->set_rules('seat', $this->lang->line("base_seat"), 'callback_check_seat['.$this->input->post("code")."]");
			$this->form_validation->set_rules('username', $this->lang->line("base_username"), "required|is_unique[users.username]");
			$this->form_validation->set_rules('password', $this->lang->line("base_password"), "required|min_length[4]");
			$this->form_validation->set_rules('password2', $this->lang->line("base_password_again"), "required|matches[password]");
			$this->form_validation->set_rules('email', $this->lang->line("base_email"), "required|valid_email");
						
			if ($this->form_validation->run() == FALSE) {
			
				$this->template->set_partial("msg","partials/alert", array("message" => validation_errors()));
				
			} else {

				if($this->users_model->insert($this->input->post("seat"),
					array(
						"username" => $this->input->post("username"),
						"password" => $this->lan_auth->createPassword($this->input->post("password")),
						"email" => $this->input->post("email"),
						"ip" => $this->input->ip_address(),
						"type" => 1
					)
				)) {

					$this->msg->addmsg("partials/alert-success",$this->lang->line("base_username_created"));
					redirect("/");

				}

			}

		}

		$this->template
			->title($this->lang->line("base_register"))
			->build("register");

	}

	function check_seat($seat, $code) {

		$this->load->model("users_model");

		if(!$this->users_model->checkSeat($seat,strtoupper($code))) {
			$this->form_validation->set_message('check_seat', $this->lang->line("base_incorrect_code"));
			return false;
		}

	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */