<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->lan_auth->setLevel(1);
		
		if ($this->lan_auth->logged())  {

			$this->template->set_partial("login", "partials/loggedin",array("username" => $this->session->userdata('username')));
			$this->template->set_partial("navigation","partials/navigation", array(
				"dashboard_active" => "active"
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

		$this->load->model("schedule_model");

		$schedule = $this->schedule_model->getAll();
		
		$scheduledata = "";

		foreach($schedule as $day => $day_events) {

			$daydata = "";

			foreach($day_events as $event) {

				if(time()>strtotime($event['start']) && time()<strtotime($event['end'])) $ongoing  = "event-ongoing"; else $ongoing = "event";

				$daydata .= $this->load->view("partials/".$ongoing,array(
					"title" => $event['name'],
					"id" => $event['id'],
					"time" => date("H:i",strtotime($event['start']))
				), true);

			}

			$scheduledata .= $this->load->view("partials/day",array(
				"day"=>$this->lang->line("base_weekday_".date("N",strtotime($day)))." ".date("d.m.",strtotime($day)),
				"events" => $daydata
			),true);

		}

		$this->template->inject_partial("schedule",$scheduledata);

		$this->template->inject_partial("nick",$this->session->userdata('username'));

		$this->template->inject_partial("civars",json_encode(array(
			"base_url" => base_url(),
			"base_incorrect_url" => $this->lang->line("base_incorrect_url"),
			"base_loading" => $this->lang->line("base_loading")
		)));

		$this->template->append_metadata('<script src="/js/dashboard.js"></script>');

		$this->load->library("lastfm");

		$this->config->load("lastfm");

		$lastfm_tracks = $this->lastfm->call("user.getRecentTracks",array("user"=>$this->config->item("lastfm_user_account"),"limit"=>3));

		if(isset($lastfm_tracks->recenttracks->track[0]->{"@attr"}->nowplaying)) {

			$this->template->inject_partial("nowplaying",$this->load->view("partials/nowplaying",array(
				"artist" => $lastfm_tracks->recenttracks->track[0]->artist->{"#text"},
				"title" => $lastfm_tracks->recenttracks->track[0]->name,
				"album" => $lastfm_tracks->recenttracks->track[0]->album->{"#text"},
				"cover" => $lastfm_tracks->recenttracks->track[0]->image[1]->{"#text"}
			),true));

			unset($lastfm_tracks->recenttracks->track[0]);

		} else {

			$this->template->inject_partial("nowplaying",$this->load->view("partials/nothingplaying",array(),true));

		}

		$playeddata = "";

		foreach($lastfm_tracks->recenttracks->track as $track) {

			$playeddata .= $this->load->view("partials/recentlyplayed",array(
				"artist" => $track->artist->{"#text"},
				"title" => $track->name,
				"album" => $track->album->{"#text"},
				"cover" => $track->image[1]->{"#text"}
			),true);

		} 

		$this->template->inject_partial("recentlyplayed",$playeddata);
		
		$this->template
			->title($this->lang->line("dashboard"))
			->build("dashboard");

	}

	public function irc() {

		$data['nick'] = $this->session->userdata('username');

		$data['channel'] = "#changeme";

		echo $this->load->view("irc", $data );

	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */