<?php

	$excel_full_engine = true;
	$q_dataset_default = false;
	$q_dataset_attempted = false;
	$q_dataset = "C:\\Users\\Stefano\\quiz-dataset-new.xls";
	$q_dataset_sheet = 5;
	$filename = './files/state.txt';
	$LF = (PHP_SAPI === 'cli') ? "\n" : "<br />";
	
	$i = 0;
	$default_t[$i++] = array( "id" => 1, "name" => "Team 1", "score" => 0 );
	$default_t[$i++] = array( "id" => 2, "name" => "Team 2", "score" => 0 );
	$default_t[$i++] = array( "id" => 3, "name" => "Team 3", "score" => 0 );
	$default_t[$i++] = array( "id" => 4, "name" => "Team 4", "score" => 0 );

	$i = 0;
	$default_q[$i++] = array ( "trivia", "100", "q", "a" );
	$default_q[$i++] = array ( "trivia", "200", "q", "a" );
	$default_q[$i++] = array ( "trivia", "300", "q", "a" );
	$default_q[$i++] = array ( "trivia", "400", "q", "a" );
	$default_q[$i++] = array ( "trivia", "500", "q", "a" );
	$default_q[$i++] = array ( "malware", "100", "q", "a" );
	$default_q[$i++] = array ( "malware", "200", "q", "T:24", "F:311");
	$default_q[$i++] = array ( "malware", "300", "q", "a" );
	$default_q[$i++] = array ( "malware", "400", "q", "a" );
	$default_q[$i++] = array ( "malware", "500", "q", "a" );
	$default_q[$i++] = array (  "appsec", "100", "q", "a" );
	$default_q[$i++] = array (  "appsec", "200", "q", "a" );
	$default_q[$i++] = array (  "appsec", "300", "q", "a" );
	$default_q[$i++] = array (  "appsec", "400", "q", "a" );
	$default_q[$i++] = array (  "appsec", "500", "q", "a" );
	$default_q[$i++] = array (  "web", "100",  "q", "a" );
	$default_q[$i++] = array (  "web", "200",  "q", "a" );
	$default_q[$i++] = array (  "web", "300",  "q", "a" );
	$default_q[$i++] = array (  "web", "400",  "q", "T:24", "F:311");
	$default_q[$i++] = array (  "web", "500",  "q", "a" );
	$default_q[$i++] = array (  "misc", "100", "q", "a" );
	$default_q[$i++] = array (  "misc", "200", "q", "a" );
	$default_q[$i++] = array (  "misc", "300", "q", "a" );
	$default_q[$i++] = array (  "misc", "400", "q", "a" );
	$default_q[$i++] = array (  "misc", "500", "q", "a" );

?>
