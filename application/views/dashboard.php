<div class="span9">

	<a href="<?php echo base_url('dashboard/irc');?>" class="btn btn-primary" id="open-irc"><i class="icon-white icon-comment"></i> <?php echo lang("base_open_irc"); ?></a>

	<a data-toggle="modal" data-target="#song-request-modal" class="btn btn-success" id="send-song-request"><i class="icon-white icon-music"></i> <?php echo lang("base_send_song_request"); ?></a>

	<br/>
	<br/>

	<div id="player">
		<div id="nowplaying">
			<h3><?php echo lang("base_now_playing"); ?></h3>
			<?php echo $template['partials']['nowplaying']; ?>
		</div>
		<div id="recentlyplayed">
			<h3><?php echo lang("base_recently_played"); ?></h3>
			<?php echo $template['partials']['recentlyplayed']; ?>
		</div>
	</div>

</div>

<div class="span3">

	<div id="schedule">
		<h3><?php echo lang("base_schedule"); ?></h3>

		<?php echo $template['partials']['schedule']; ?>
	</div>

</div>

<div class="modal hide fade" id="song-request-modal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo lang("base_suggest_song"); ?></h3>
  </div>
  <div class="modal-body">
    <a href="#" id="spotify-link" class="btn"><?php echo lang("base_song_in_spotify"); ?></a>
	<a href="#" id="not-spotify-link" class="btn"><?php echo lang("base_song_not_in_spotify"); ?></a>

	<div id="not-spotify">

		<br/>
		
		<label><?php echo lang("base_song_name"); ?></label>
		<input type='text' id="title-input" /></label><br/>

		<label><?php echo lang("base_album"); ?></label>
		<input type='text' id="album-input" /></label><br/>

		<label><?php echo lang("base_artist"); ?></label>
		<input type='text' id="artist-input" /></label><br/>
		<button type="button" id="send" class="btn btn-primary"><?php echo lang("base_send"); ?></button>

	</div>

	<div id="spotify">

		<p><?php echo lang("base_type_spotify_url"); ?></p>

		<input type='text' id='url' placeholder="http://open.spotify.com..." /> <button type="button" id="check" class="btn btn-primary"><?php echo lang("base_search"); ?></button>

		<div id="result">
		</div>

		<button type="button" id="send-spotify" class="btn btn-primary"><?php echo lang("base_send"); ?></button>

	</div>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal"><?php echo lang("base_close"); ?></a>
  </div>
</div>