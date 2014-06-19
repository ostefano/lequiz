<!--

var remote_push = true;
var remote_push_server = "";

var default_time_to_think = 3; // 30 seconds
var force_default_load = false;
var force_questions_done = false;

var classes = [ "trivia", "web", "misc", "appsec", "malware" ]
var levels = [ "100", "200", "300", "400", "500" ]
var levels_points = {
    "100" : 1,
    "200" : 2,
    "300" : 3,
    "400" : 4,
    "500" : 5}

var statusdata_teams;
var statusdata_questions;

var default_statusdata_teams = [
			{'name' : "Team 1", 'score' : "0" },
			{'name' : "Team 2", 'score' : "10" },
			{'name' : "Team 3", 'score' : "5" },
			{'name' : "Team 4", 'score' : "1" },
			{'name' : "Team A", 'score' : "2"}]

var default_statusdata_questions = [
    		{'id': "1", 'class' : "trivia", 	'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "2", 'class' : "web", 		'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "3", 'class' : "misc", 		'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "4", 'class' : "appsec", 	'level' : "100", 'attempted': true , "q" : "Question?", "a" : "42"}, 
    		{'id': "5", 'class' : "malware", 	'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "6", 'class' : "trivia", 	'level' : "200", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "7", 'class' : "web", 		'level' : "200", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "8", 'class' : "misc", 		'level' : "200", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "9", 'class' : "appsec", 	'level' : "200", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "10", 'class' : "malware", 	'level' : "200", 'attempted': false, "q" : "Question?", "a" : ["T:24", "F:411"]}, 
    		{'id': "11", 'class' : "trivia", 	'level' : "300", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "12", 'class' : "web", 		'level' : "300", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "13", 'class' : "misc", 		'level' : "300", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "14", 'class' : "appsec", 	'level' : "300", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "15", 'class' : "malware", 	'level' : "300", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "16", 'class' : "trivia", 	'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "17", 'class' : "web", 		'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "18", 'class' : "misc", 		'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "19", 'class' : "appsec", 	'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "20", 'class' : "malware", 	'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "21", 'class' : "trivia", 	'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "22", 'class' : "web", 		'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "23", 'class' : "misc", 		'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "24", 'class' : "appsec", 	'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "25", 'class' : "malware", 	'level' : "400", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "26", 'class' : "trivia", 	'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "27", 'class' : "web", 		'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "28", 'class' : "misc", 		'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "29", 'class' : "appsec", 	'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "30", 'class' : "malware", 	'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "31", 'class' : "trivia", 	'level' : "200", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "32", 'class' : "web", 		'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "33", 'class' : "misc", 		'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "34", 'class' : "appsec", 	'level' : "100", 'attempted': false, "q" : "Question?", "a" : "42"}, 
    		{'id': "35", 'class' : "malware", 	'level' : "100", 'attempted': false, "q" : "Question?", "a" : ["T:42", "F:41"] }] 

-->