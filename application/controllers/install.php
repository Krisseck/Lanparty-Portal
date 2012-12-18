<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Install extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

	}

	public function generate_seats($seats)
	{

		$this->load->model("settings_model");
		$this->load->model("users_model");

		$seats = explode(":", $seats);

		foreach($seats as $table) {

			$tabledata = explode("-", $table);

			$tables[$tabledata[0]] = $tabledata[1];

		}

		$this->settings_model->set("seats",serialize($tables));

		foreach($tables as $table => $count) {

			for($i=1;$i<=$count;$i++) {

				$code = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',6)),0,6);

				$this->users_model->insertEmptySeat($table.$i,$code);

			}

		}

	}

	public function add_admin($username,$password) {

		$this->load->model("users_model");

		$this->users_model->insertNewUser(array(
			"username" => $username,
			"password" => $this->lan_auth->createPassword($password),
			"ip" => $this->input->ip_address(),
			"type" => 9
		));

	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */