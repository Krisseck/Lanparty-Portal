var CIVars = {};

$(document).ready(function() {

	if($("#civars").text()!="") {
	
		CIVars = eval("("+$("#civars").text()+")");
	
	}

	$("#spotify, #not-spotify, #send-spotify, #send").hide();

	var start = false;

	$("#not-spotify-link").click(function() {
		if($("#not-spotify").is(":hidden")) {
			$("#spotify").slideUp();
			$("#not-spotify").slideDown();
			$("#not-spotify-link").prepend("&#10003; ");
			if(start) $("#spotify-link").text($("#spotify-link").text().substr(2));
			start = true;
		}
		return false;
	});

	$("#spotify-link").click(function() {
		if($("#spotify").is(":hidden")) {
			$("#not-spotify").slideUp();
			$("#spotify").slideDown();
			$("#spotify-link").prepend("&#10003; ");
			if(start) $("#not-spotify-link").text($("#not-spotify-link").text().substr(2));
			start = true;
		}
		return false;
	});

	$("#album-input, #title-input, #artist-input").keypress(function() {
		
		if($("#album-input").val()!="" || $("#title-input").val()!="" || $("#artist-input").val()!="") {
			$("#send").fadeIn();
		} else {
			$("#send").fadeOut();
		}

	}).blur(function() {
		
		if($("#album-input").val()=="" && $("#title-input").val()=="" && $("#artist-input").val()=="") {
			$("#send").fadeOut();
		}
	});

	$("#check").click(function() {

		url = $("#url").val();

		if(url!="") {
			if(url.substr(0,14)=="spotify:track:") {
				url = "http://open.spotify.com/track/"+url.substr(14);		
			} else if(url.substr(0,30)!="http://open.spotify.com/track/") {
				alert(CIVars['base_incorrect_url']);
				return false;
			}

			$("#url").val(url);

			$.ajax({
				url: CIVars['base_url']+"songs/fetch",
				data: "url="+url,
				type: "POST",
				beforeSend: function() {
					$("#result").html("").append(CIVars['base_loading']);
				},
			  	success: function(data) {
			    	eval("data = "+data+";");
			    	$("#result").html("").append("<div id='title'>"+data['title']+"</div><div id='album'>"+data['album']+"</div><div id='artist'>"+data['artist']+"</div><img src='"+data['coverart']+"' />");
			    	$("#send-spotify").fadeIn();
			    }
			});

		}
		
		return false;

	});

	$("#send").click(function() {

		$.ajax({
			url: CIVars['base_url']+"songs/send",
			type: "POST",
			data: "title="+$("#title-input").val()+"&album="+$("#album-input").val()+"&artist="+$("#artist-input").val(),
			beforeSend: function() {
				$("#send").replaceWith("<p id='send-spotify'>"+CIVars['base_loading']+"</p>");
			},
		  	success: function(data) {
		    	if(data=="OK")
		    		window.location = CIVars['base_url']+"songs";	   
		    	}
		});

		
		return false;

	});

	$("#send-spotify").click(function() {

		$.ajax({
			url: CIVars['base_url']+"songs/send",
			data: "url="+$("#url").val(),
			type: "POST",
			beforeSend: function() {
				$("#send-spotify").replaceWith("<p id='send-spotify'>"+CIVars['base_loading']+"</p>");
			},
		  	success: function(data) {
		    	if(data=="OK")
		    		window.location = CIVars['base_url']+"songs";
		    }
		});

		return false;

	});

});