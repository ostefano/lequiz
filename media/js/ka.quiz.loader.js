<!--

$(document).ready(function() {

	load_options();
	load_state(force_default_load, force_questions_done);
	document.body.style.zoom="100%";

	$("[id=load_state]").click(function(e) {
		e.preventDefault();
		load_state(force_default_load, force_questions_done);
	});

	$("[id=save_state]").click(function(e) {
		e.preventDefault();
		save_state();
	});

	$("[id=reset_state]").click(function(e) {
		e.preventDefault();
		reset_state();
	});
	
	$("[id=quiz_modal_time_start]").click(function(e) {
		e.preventDefault();
		start_button_clicked(this);
	});

	$('#quiz_modal').on('show.bs.modal', function () {
		modal_window_show();
	})
	
	$('#quiz_modal').on('hidden.bs.modal', function () {
		modal_window_hide();	
	})
	
});

-->