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

		var search = new RegExp($(this).find("input").val(),"i");

		$("#seats .seat.arrived").each(function() {

			if( search.test($.trim($(this).find(".username").text())) || search.test($.trim($(this).find(".ip").text())) ) {

				$(this).addClass("chosen");

			}

		});

		return false;

	});


});