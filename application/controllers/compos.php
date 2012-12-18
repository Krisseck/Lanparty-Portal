<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Compos extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->lan_auth->setLevel(1);
		
		if ($this->lan_auth->logged())  {

			$this->template->set_partial("login", "partials/loggedin",array("username" => $this->session->userdata('username')));
			$this->template->set_partial("navigation","partials/navigation", array(
				"compos_active" => "active"
			));

		}

		if($this->session->userdata("type")==9) {

			$this->template->set_partial("adminlogo","partials/admin-logo");

		} else {

			$this->template->inject_partial("adminlogo","");
			
		}
		$this->msg->generate_msg();

		$this->load->model("compos_model");

	}

	public function index()
	{

		$compos = $this->compos_model->getAll();
		
		$composdata = "";

		$this->load->library("Binarybeast");

		$tmp_bb_compos = $this->binarybeast->tournament_list_my();

		$bb_compos = array();

		foreach($tmp_bb_compos->list as $compo) {

			$bb_compos[$compo->tourney_id] = $compo;

		}

		foreach($compos as $compo) {

			if($compo['binarybeast']!="") {

				if(array_key_exists($compo['binarybeast'], $bb_compos)) {

					$compo['max_teams'] = $bb_compos[$compo['binarybeast']]->max_teams;
					$compo['c_teams'] = $bb_compos[$compo['binarybeast']]->teams_joined_count;

					if($bb_compos[$compo['binarybeast']]->status=="Active") {

						$compo['binarybeast_height'] = 500;

					} else {

						$compo['binarybeast_height'] = 44+20+28+11+(45*$compo['c_teams']);

					}

				}

			} else {

				$compo['max_teams'] = 0;
				$compo['teams'] = 0;

			}

			$composdata .= $this->load->view("partials/compo",$compo,true);

		}

		$this->template->inject_partial("compos",$composdata);
		
		$this->template
			->title($this->lang->line("base_tournaments"))
			->build("compos");

	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */