$(document).ready(function() {
	
	var xOffset = 10;
	var yOffset = 20;
	
	$(".arrived").hover(function(e){											  									  
		$("body").append("<p id='tooltip'>"+$(this).find(".username").text()+"<br/>"+$(this).find(".ip").text()+"</p>");
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");
	},
	function(){
		$("#tooltip").remove();
	});	
	$(".arrived").mousemove(function(e){
	$("#tooltip")
		.css("top",(e.pageY - xOffset) + "px")
		.css("left",(e.pageX + yOffset) + "px");
	});

	$("#search-seats").submit(function() {

		$("#seats .seat.arrived").removeClass("chosen");

		var search = $(this).find("input").val();

		$("#seats .seat.arrived").each(function() {

			if($.trim($(this).find(".username").text()).indexOf(search) != -1 || $.trim($(this).find(".ip").text()).indexOf(search) != -1) {

				$(this).addClass("chosen");

			}

		});

		return false;

	});


});