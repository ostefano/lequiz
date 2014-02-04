<!--

var time_left;
var think_timer_countdown;
var current_q_index;
var current_q_multiple;

$.fn.hasAnyClass = function(classesToCheck) {
	for (var i = 0; i < classesToCheck.length; i++) {
		if (this.hasClass(classesToCheck[i])) {
			return classesToCheck[i];
		}
	}
	return false;
}

function toggle_checkbok(e) {
	var buttonClasses = ['btn-primary','btn-danger','btn-warning','btn-success','btn-info','btn-inverse'];
	if ($(e.target).attr('class-toggle') != undefined && !$(e.target).hasClass('disabled')) {
		var btnGroup = $(e.target).parent('.btn-group');
		var btnToggleClass = $(e.target).attr('class-toggle');
		var btnCurrentClass = $(e.target).hasAnyClass(buttonClasses);    
		if (btnGroup.attr('data-toggle') == 'buttons-radio') {
			if($(e.target).hasClass('active')) {
				return false;
			}
			var activeButton = btnGroup.find('.btn.active');
			var activeBtnClass = activeButton.hasAnyClass(buttonClasses);          
			activeButton.removeClass(activeBtnClass).addClass(activeButton.attr('class-toggle')).attr('class-toggle',activeBtnClass);
		}
		$(e.target).removeClass(btnCurrentClass).addClass(btnToggleClass).attr('class-toggle',btnCurrentClass);
	}
}

function toggle_question_element(q_index, set_to_attempted) {
	var k_class = statusdata_questions[q_index]['class'];
	var k_level = statusdata_questions[q_index]['level'];
	var k_id = statusdata_questions[q_index]['id'];
	var entry = k_class + "_" + k_level + "_" + k_id;
	if(set_to_attempted === false) {
		$("[id=quiz_question][entry='" + entry + "']")
			.removeClass()
			.addClass("btn btn-success");
	} else {
		$("[id=quiz_question][entry='" + entry + "']")
			.removeClass()
			.addClass("btn btn-warning");
	}
}

function build_gui() {

	// (1a) Fill the grid with buttons
	classes.forEach(function(c) {
		levels.forEach(function(l) {
			var items = statusdata_questions.filter(function(item) {
				if(item.level === l && item.class === c) {
					return item;
				}
			});
			var button = $('#template').jqote(items);
			if (button === "") {
				button = "&nbsp;";
			}
			var toadd = "<div class='quiz_question_container'>" + button + "</div>";
			$(toadd).appendTo($('#level_' + l + "_" + c));
		});
	});

	// (1b) Artificially click the button if the current (just loaded) state says so
	for (var q_index in statusdata_questions) {
		toggle_question_element(q_index, statusdata_questions[q_index]['attempted']);
	}

	// (2) Regenerate all elements depending on the number and name of the teams (3 items)
	$($('#template_team_head').jqote(statusdata_teams)).appendTo($('#team_names'));
	$($('#template_team_scores').jqote(statusdata_teams)).appendTo($('#team_scores'));
	$($('#template_quiz_modal_teams').jqote(statusdata_teams)).appendTo($('#quiz_modal_teams'));

	// (3) We need to re-attach handlers to what we rebuilt (question buttons, team buttons)
	$("[id=quiz_question]").click(function(e) {
		e.preventDefault();
		open_modal_window(this);
	});

	$('.btn-group > .btn, .btn[data-toggle="button"]').click(function(e) {
		toggle_checkbok(e);
	});
}

function destroy_gui() {

	// (1) destroy all the question buttons
	$("div").remove(".quiz_question_container");
	// (2) destroy all structures depending on team names and scores (3 items)
	$("th").remove(".th_team_names");
	$("td").remove(".td_team_scores");
	$("#quiz_modal_teams").empty();
}

function reset_state() {
	
	// (1) Reset team scores (set to 0) 
	for (var d in statusdata_teams) {
		statusdata_teams[d]['score'] = "0";
		var k_name = statusdata_teams[d]['name'];
		$("[id='" + k_name + "']").text("0");
	}

	// (2) Mark all questions and toggle the button
	for (var q in statusdata_questions) {
		statusdata_questions[q]['attempted'] = false;
		toggle_question_element(q, false);
	}
}

function load_state(force_default, force_attempted) {

	if(force_default) {
		statusdata_teams = default_statusdata_teams;
		statusdata_questions = default_statusdata_questions;
		if(force_attempted) {
			for(var q in statusdata_questions) {
				statusdata_questions[q]['attempted'] = true;
			}
		}
		$("#head_row_msg").html("loaded default");
		destroy_gui();
		build_gui();
		return;
	}

	$.ajax({
		url: "state.php?action=load",
		type: 'GET',
		async : false,
		success: function(returned_data) {
			var server = jQuery.parseJSON(returned_data);
			if (server.status === 200) {
				statusdata_teams = JSON.parse(server.teams);
				statusdata_questions = JSON.parse(server.questions);
				$("#head_row_msg").html("load from server");
			} else {
				statusdata_teams = default_statusdata_teams;
				statusdata_questions = default_statusdata_questions;
				$("#head_row_msg").html(server.message);
			}
			if(force_attempted) {
				for(var q in statusdata_questions) {
					statusdata_questions[q]['attempted'] = true;
				}
			}
			destroy_gui();
			build_gui();
		}
	});
}

function save_state() {
	var form_data = {
		teams: JSON.stringify(statusdata_teams),
		questions: JSON.stringify(statusdata_questions)
	};

	$.ajax({
		url: "state.php?action=save",
		type: 'POST',
		async : false,
		data: form_data,
		success: function(returned_data) {
			var server = jQuery.parseJSON(returned_data);
			if (server.status === 200) {
				var current_date = new Date();
				var h = current_date.getHours();
				var m = current_date.getMinutes();
				var s = current_date.getSeconds();
				h = h < 10 ? "0" + h : h;
				m = m < 10 ? "0" + m : m;
				s = s < 10 ? "0" + s : s;
				var time = h + ":" + m + ":" + s;
				$("#head_row_time").html(time);
				$("#head_row_msg").html(server.message);
			} else {
				if(server.message != undefined) {
					$("#head_row_msg").html(server.message);
				}
			}
		}
	});
}

function set_to_init_state() {
	// Reset variables to a starting point
	clearInterval(think_timer_countdown);
	time_left = default_time_to_think;
	// Do the magic on the gui
	$('#quiz_modal_answer').hide();

	startButton = $("[id=quiz_modal_time_start]");
	startButton.removeClass().addClass('btn btn-success');
	startButton.attr('status',"ready_start");
	startButton.text("Start");
	$('#time_left_seconds').html(time_left);
}

function set_to_done_state() {
	$('#quiz_modal_answer').show();
	startButton = $("[id=quiz_modal_time_start]");
	startButton.removeClass("btn-warning").addClass("btn-info disabled");
	startButton.attr('status', "disabled");
	startButton.text("Show answer");
	$('#time_left_seconds').html("0");

	if(current_q_multiple === true) {
		for (var i = 0; i < $("#list-group-answers > *").length; i++) {
			var v = $("#list-group-answers > *").eq(i).attr("correct");
			if ($.parseJSON(v) === true) {
				$("#list-group-answers > *").eq(i).addClass("alert-success");
			}		
		}
	}
}

function timer_countdown_function()  {
	time_left--;
	$('#time_left_seconds').html(time_left);

	if(time_left <= 0) {
		// Clear the countdown interval
		clearInterval(think_timer_countdown);
		startButton = $("[id=quiz_modal_time_start]");
		startButton.removeClass("btn-warning").addClass("btn-info");
		startButton.attr('status','ready_show_answer');
		startButton.text("Show answer");
		$('#time_left_seconds').html("0");
	}
}

function open_modal_window(target) {

	current_q = $(target).attr('entry');
	current_q_id = current_q.split('_')[2];
	current_q_index = statusdata_questions.map(function(e) { return e.id; }).indexOf(current_q_id);

	if (typeof current_q_index === "undefined" || current_q_index === -1) {
		alert ("Can't extract ID");
		current_q_index = -1;
		return false;
	}

	if (typeof statusdata_questions[current_q_index] === "undefined") {
		alert ("This question doesn't exist!");
		current_q_index = -1;
		return false;
	} 

	que = statusdata_questions[current_q_index].q;
	ans = statusdata_questions[current_q_index].a;
	tim = default_time_to_think;
	lab = statusdata_questions[current_q_index].class + " " + statusdata_questions[current_q_index].level;

	// Hide immediately the answer!
	$('#quiz_modal_answer').hide();
	$('#quiz_modal_title').html(lab);
	$('#quiz_modal_question').html(que);
	if (ans instanceof Array) {
		current_q_multiple = true;
		var h = "<ul id='list-group-answers' class='list-group'>";
		for (var i = 0; i < ans.length; i++) {
			ans_iscorrect = ans[i].substring(0,1) === "T" ? true : false;
			ans_text = ans[i].substring(2);
			h += "<li id='list-group-answer' class='list-group-item' style='text-align: left' correct='"+ans_iscorrect+"' >";
			h += "<span style='font-weight: bold'>" + i + ")</span> ";
			h += ans_text;
			h += "</li>";
		}
		h += "</ul>";
		// remove all elements (multiple answers)
		$("ul[id=list-group-answers]").remove();
		// empty the answer (the actual one in case there was one)
		$('#quiz_modal_answer_text').empty();
		// add the multiple answers before the single one (now removed)
		$('#quiz_modal_answer').before(h);
	} else {
		current_q_multiple = false;
		var h = "<div class='alert alert-success'>";
		h += ans;
		h += "</div>";
		// remove all elements (multiple answers)
		$("ul[id=list-group-answers]").remove();
		// no need to delete the previous answer, just replace it
		$('#quiz_modal_answer_text').html(h);
	}
	
	$('#time_left_seconds').html(default_time_to_think);			
	$('#quiz_modal').modal({
		backdrop: "static"
	});

	return;
}

function start_button_clicked(target) {
	if ($(target).attr('status') == "ready_start") {
		$(target).removeClass("btn-success").addClass("btn-warning");
		$(target).text("Pause");
		$(target).attr('status',"waiting_pause");			
		think_timer_countdown = setInterval(timer_countdown_function, 1000);
		// Enable the rush functionality	
		$('#quiz_modal_time_left_rush').on('click', function(e) { time_left = 0 });
		$('#quiz_modal_time_left_rush').css({"color" : "black", "text-decoration" : "underline"});		
	} else if ($(target).attr('status') == "waiting_pause") {
		$(target).text("Resume");
		$(target).attr('status',"waiting_unpause");		
		clearInterval(think_timer_countdown);			
	} else if ($(target).attr('status') == "waiting_unpause") {
		$(target).text("Pause");
		$(target).attr('status',"waiting_pause");		
		think_timer_countdown = setInterval(timer_countdown_function, 1000);			
	} else if ($(target).attr('status') == "ready_show_answer") {
		statusdata_questions[current_q_index].attempted = true;
		toggle_question_element(current_q_index, true);
		set_to_done_state();
	} else if ($(target).attr('status') == "disabled") {
		return false;
	}
		
	return true;
}

function modal_window_hide() {
	// Reset the start button
	set_to_init_state();
	// Save the level as we might need it later
	var level = statusdata_questions[current_q_index].level
	// Reset the question index
	current_q_index = -1;
	
	// Now check if team_checkbox have something
	for (var d in statusdata_teams) {
		var k_name = statusdata_teams[d]['name'];
		var k_score = statusdata_teams[d]['score'];	
		element = document.getElementById('team_checkbox_' + k_name);
		if($(element).is(':checked')) {
			var k_int_score = +k_score;
			k_int_score += levels_points[level];
			statusdata_teams[d]['score'] = k_int_score;
			$("[id='" + k_name + "']").text(k_int_score);
			element.parentElement.click();
		}		
	}
	save_state();
}

function modal_window_show() {
	// No matter what
	// reset the ability to rush 
	$('#quiz_modal_time_left_rush').unbind('click');
	$('#quiz_modal_time_left_rush').css({"color" : "black", "text-decoration" : "none"});

	// If the question has been done already, set it done state
	// otherwise hide the answer and etc etc
	if (statusdata_questions[current_q_index].attempted === true) {
		set_to_done_state();
	} else {
		set_to_init_state();
	}
}

-->