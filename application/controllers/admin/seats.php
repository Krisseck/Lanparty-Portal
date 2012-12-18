<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Seats extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->lan_auth->setLevel(9);

		$this->template->set_partial("login", "partials/loggedin",array("username" => $this->session->userdata('username')));
		$this->template->set_partial("adminlogo","partials/admin-logo");
		$this->template->set_partial("navigation","partials/navigation-admin", array(
			"seats_active" => "active"
		));

		$this->msg->generate_msg();

	}

	public function index()
	{

		$this->load->model("users_model");

		$seats = $this->settings_model->get("seats");

		$seatsdata = "";

		foreach($seats as $table => $count) {

			$tabledata = "";

			for($i=1;$i<=$count;$i++) {

				$user = $this->users_model->getUserBySeat($table.$i);

				if($user['username']) {

					$username = $user['username'];
					$class = "arrived";
					$ip = $user['ip'];

				} else {

					$username = "";
					$class = "";
					$ip = "";

				}

				$tabledata .= $this->load->view("partials/table-seat",array(
					"name" => $table.$i,
					"username" => $username,
					"class" => $class,
					"ip" => $ip
				),true);

			}

			$seatsdata .= $this->load->view("partials/table",array(
				"table" => $table,
				"seats" => $tabledata
			),true);

		}

		$this->template->inject_partial("seats",$seatsdata);

		$this->template->append_metadata('<script src="/js/seats-admin.js"></script>');
		
		$this->template
			->title($this->lang->line("base_seats"))
			->build("seats-admin");

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */