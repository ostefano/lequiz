<?php

	require_once('config.inc.php');
	require_once('phpexcel/PHPExcel.php');
	require_once('php-spreadsheetreader/SpreadsheetReaderFactory.php');

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
			"id" => strval($i), 
			"class" => $element[0],	
			"level" => strval($element[1]),
			"attempted" => $q_dataset_attempted,
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
    	echo "[ ] (".$default_t[$i]['name'].") = ".$default_t[$i]['score'].$LF;
	}
	echo "[*] Number of teams: ".count($teams).$LF;

	/*
	 *	NOW JSON ENCODE (TWICE :S) AND OUTPUT TO FILE
	 */
	echo $LF."[*] Writing to state file".$LF;
	$q_encoded = json_encode($questions);
	$t_encoded = json_encode($teams);
	$packet = array(
		"questions" => $q_encoded,
		"teams" => $t_encoded
	);
	$p_encoded = json_encode($packet);
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

?> 
