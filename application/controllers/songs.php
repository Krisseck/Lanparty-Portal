<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Songs extends CI_Controller {

	function __construct()
	{
		parent::__construct();	

		$this->lan_auth->setLevel(1);
		
		if ($this->lan_auth->logged())  {

			$this->template->set_partial("login", "partials/loggedin",array("username" => $this->session->userdata('username')));
			$this->template->set_partial("navigation","partials/navigation", array(
				"songs_active" => "active"
			));

		}

		if($this->session->userdata("type")==9) {

			$this->template->set_partial("adminlogo","partials/admin-logo");

		} else {

			$this->template->inject_partial("adminlogo","");
			
		}

		$this->msg->generate_msg();

		$this->load->model("songs_model");

	}

	public function index()
	{

		$songs = $this->songs_model->getTop();
		
		$songsdata = "";

		foreach($songs as $song) {

			$songsdata .= $this->load->view("partials/song",$song,true);

		}

		$this->template->inject_partial("songs",$songsdata);

		$this->template->append_metadata('<script src="/js/songs.js"></script>');

		$this->template->inject_partial("civars",json_encode(array(
			"base_url" => base_url(),
			"base_incorrect_url" => $this->lang->line("base_incorrect_url"),
			"base_loading" => $this->lang->line("base_loading")
		)));
		
		$this->template
			->title($this->lang->line("base_song_requests"))
			->build("songs");

	}

	public function fetch()
	{

		if($this->input->post('url')!="" && substr($this->input->post('url'),0,30)=="http://open.spotify.com/track/") {

			$this->load->helper("simple_html_dom");

			$html = file_get_html($this->input->post('url'));

			echo json_encode(array(
				"coverart" => trim($html->find(".large-image-column .album-cover-art img",0)->src),
				"title" => trim($html->find(".player-header h1",0)->plaintext),
				"artist" => trim($html->find(".player-header h2 a",0)->plaintext),
				"album" => trim($html->find(".omega h3 a",0)->plaintext)
			));

		}

	}

	public function send() {

		if($this->input->post('url') != "" && (substr($this->input->post('url'),0,30)=="http://open.spotify.com/track/" || substr($this->input->post('url'),0,14)=="spotify:track:")) {

			if(substr($this->input->post('url'),0,14)=="spotify:track:") {

				$url = "http://open.spotify.com/track/".substr($this->input->post('url'),14);	

			} else {

				$url = $this->input->post('url');

			}

			$this->load->helper("simple_html_dom");

			$html = file_get_html($url);

			$title = trim($html->find(".player-header h1",0)->plaintext);
			$artist = trim($html->find(".player-header h2 a",0)->plaintext);
			$album = trim($html->find(".omega h3 a",0)->plaintext);
			$url = $url;

		} else if($this->input->post('album')!="" || $this->input->post('artist')!="" || $this->input->post('title')!="") {
			
			$title = $this->input->post('title');
			$artist = $this->input->post('artist');
			$album = $this->input->post('album');
			$url = "";
		}

		if($this->songs_model->insert($title,$artist,$album,$url)) {

			$this->msg->addmsg("partials/alert-success", $this->lang->line("base_thanks_song_request"));

			echo "OK";

		} else {

			echo "ERROR";

		}

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */