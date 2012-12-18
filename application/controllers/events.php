<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->lan_auth->setLevel(1);
		
		if ($this->lan_auth->logged())  {

			$this->template->set_partial("login", "partials/loggedin",array("username" => $this->session->userdata('username')));
			$this->template->set_partial("navigation","partials/navigation", array(
				"events_active" => "active"
			));

		}

		if($this->session->userdata("type")==9) {

			$this->template->set_partial("adminlogo","partials/admin-logo");

		} else {

			$this->template->inject_partial("adminlogo","");
			
		}
		$this->msg->generate_msg();

		$this->load->model("schedule_model");

	}

	public function index()
	{

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
		
		$this->template
			->title($this->lang->line("base_schedule"))
			->build("events");

	}


	public function event($id)
	{

		$event = $this->schedule_model->get($id);

		if(date("d.m.Y",strtotime($event['start']))==date("d.m.Y",strtotime($event['end']))) {
			$this->template->set("time",date("H:i",strtotime($event['start']))." - ".date("H:i d.m.",strtotime($event['end'])));
		} else {
			$this->template->set("time",date("H:i d.m.",strtotime($event['start']))." - ".date("H:i d.m.",strtotime($event['end'])));
		}

		$this->template->set("description",$event['description']);
		
		$this->template
			->title($event['name'])
			->build("event");

	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */