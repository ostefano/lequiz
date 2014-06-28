<?php

	require_once('config.php');
	require_once('phpexcel/Classes/PHPExcel.php');
	require_once('php-spreadsheetreader/SpreadsheetReaderFactory.php');

	$MAX_CLASSES = 5;
	$MAX_LEVELS = 5;

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

	function store_object_todisk($packet, $filename) {
		global $LF;

		$p_encoded = json_encode($packet, JSON_PRETTY_PRINT);
		$ret_val = @file_put_contents($filename, $p_encoded);	
		
		if($ret_val === false) {
			echo "[!] Script failed at some point!".$LF;
		} else {
			echo "[ ] Script ended with success!".$LF;
		}

		$ret_val = @chmod($filename , 0777);
		if($ret_val === false) {
			echo "[!] Script chmod failure!".$LF;
		} else {
			echo "[ ] Script chmod success!".$LF;
		}
	}



	// TEST where we read a JSON and print all its fields
	// Note the mess requiring a double json_decode 
	/*
	$file_content = @file_get_contents($filename);
	$document_1 = json_decode($file_content);
	$document_2 = (array)$document_1;
	$document_3_team = json_decode($document_2["teams"]);
	$document_3_ques = json_decode($document_2["questions"]);
	var_dump($document_1);
	var_dump($document_3_team);
	var_dump($document_3_ques);
	for ($i = 0; $i < count($document_3_team); $i++) {
		$temp_element = (array) $document_3_team[$i];
		echo $temp_element["name"]." - ".$temp_element["score"]."\n";
	}
	*/

	if($q_dataset_default) {
		$sheets[$q_dataset_sheet] = $default_q;
		echo "[*] Loading a default dataset (attempted=".
				var_export($q_dataset_attempted, true).")".$LF;
	} else {
		if($excel_full_engine) {
			echo "[*] Using full-fledge engine".$LF;
			$objReader = new PHPExcel_Reader_Excel5();
			$objPHPExcel = $objReader->load($q_dataset);
			$sheet = $objPHPExcel->setActiveSheetIndex($q_dataset_sheet);
			$sheets[$q_dataset_sheet] = $sheet->toArray(null,true,true,true);
			$sheets[$q_dataset_sheet] = array_values($sheets[$q_dataset_sheet]);
			for($i = 0; $i < count($sheets[$q_dataset_sheet]); $i++) {
				//$temp_array = array_values($sheets[$q_dataset_sheet][$i]);
				//$sheets[$q_dataset_sheet][$i] = $temp_array;
				$temp_array = array ();
				foreach ($sheets[$q_dataset_sheet][$i] as $value) {
					if($value != NULL && $value != "") {
						$temp_array[] = $value;
					}
				}
				$sheets[$q_dataset_sheet][$i] = $temp_array;		
			}
		} else {
			echo "[*] Using reader (LITE) engine".$LF;
			$reader = SpreadsheetReaderFactory::reader($q_dataset);
			$sheets = $reader->read($q_dataset);
		}
		echo "[*] Reading file: ".$q_dataset." (sheet=".$q_dataset_sheet.
				",attempted=".var_export($q_dataset_attempted, true).")".$LF;
	}	


	/*
	 *	READ INFORMATION ABOUT QUESTIONS
	 */
	echo $LF."[*] Reading questions (output=".$filename.")".$LF;
	$questions = array ();
	for ($i = 0; $i < count($sheets[$q_dataset_sheet]); $i++) {
		$element = $sheets[$q_dataset_sheet][$i];
		if(count($element) == 4) {
			$answer = $element[3];
		} else if (count($element) > 4) {		
			$answer = array_slice($element,3);
			$answer[0] = "T:".$answer[0];
			for ($j = 1; $j < count($answer); $j++) {
				$answer[$j] = "F:".$answer[$j];
			}
			shuffle($answer);
		} else {
			$answer = NULL;
		}
		$question = array (
			"id" => (int) $i, 
			"class" => $element[0],	
			"level" => (int) $element[1],
			"attempted" => (bool) $q_dataset_attempted,
			"q" => $element[2],
			"a" => $answer
		);

		$tempArray = array($question);
		$questions = array_merge ($questions, $tempArray);

		$q_endindex = @strpos($question['q'], ' ', 20);
		if($q_endindex === false) {
			$q_endindex = strlen($question['q']);
		}
		echo "[ ] (".$question['class'].",".$question['level'].") = ".
				substr($question['q'], 0, $q_endindex)."...".$LF;
	}
	echo "[*] Number of questions: ".count($questions).$LF;

	/*
	 *	READ INFORMATION ABOUT TEAMS (ALWAYS FROM CONFIG)
	 */
	echo $LF."[*] Reading team info".$LF;
	$teams = array ();
	for ($i = 0; $i < count($default_t); $i++) {
		$teams = array_merge ($teams, array($default_t[$i]));
		echo "[id=".$default_t[$i]['id']."] (".$default_t[$i]['name'].") = ".$default_t[$i]['score'].$LF;
	}
	echo "[*] Number of teams: ".count($teams).$LF;

	/*
	 *	NOW JSON ENCODE (TWICE :S) AND OUTPUT TO FILE
	 */
	echo $LF."[*] Writing to state file".$LF;
	/*
	$q_encoded = json_encode($questions, JSON_PRETTY_PRINT);
	$t_encoded = json_encode($teams, JSON_PRETTY_PRINT);
	$packet = array(
		"questions" => $q_encoded,
		"teams" => $t_encoded
	);*/
	$packet = array(
		"questions" => $questions,
		"teams" => $teams
	);
	store_object_todisk($packet, $filename);


	/*
	 *	DETERMINE CLASSES AND LEVES AND WRITE THE METADATA
	 */
	echo $LF."[*] Determining classes (max=".$MAX_CLASSES.")".$LF;
	$q_classes = array_map(function($a) { return $a["class"]; }, $questions);
	$q_classes = array_unique($q_classes);
	$q_classes = array_values($q_classes);
	//$q_classes = array_keys(array_flip($q_classes));
	sort($q_classes);
	for ($i = 0; $i < count($q_classes); $i++) {
		echo "[ ] ".$q_classes[$i].$LF;
	}
	if (count($q_classes) > $MAX_CLASSES) {
		die("MAXIMUM CLASSES REACHED");
	}
	echo $LF;
	for ($i = count($q_classes); $i < $MAX_CLASSES; $i++) {
		array_push($q_classes, "NULL");
	}
	for ($i = 0; $i < count($q_classes); $i++) {
		echo "[-] ".$q_classes[$i].$LF;
	}
	echo $LF."[*] Determining levels (max=".$MAX_LEVELS.")".$LF;
	$q_levels = array_unique(array_map(function($a) { return $a["level"]; }, $questions));
	$q_levels = array_keys(array_flip($q_levels));
	sort($q_levels);
	for ($i = 0; $i < count($q_levels); $i++) {
		echo "[ ] ".$q_levels[$i].$LF;
	}
	if (count($q_levels) > $MAX_LEVELS) {
		die("Maximum LEVELS REACHED");
	}
	echo $LF;
	for ($i = count($q_levels); $i < $MAX_LEVELS; $i++) {
		array_push($q_levels, "NULL");
	}
	//sort($q_levels);
	for ($i = 0; $i < count($q_levels); $i++) {
		echo "[-] ".$q_levels[$i].$LF;
	}
	$packet = array(
		"classes" => $q_classes,
		"levels" => $q_levels
	);
	store_object_todisk($packet, $filename_meta);
	

?> 
