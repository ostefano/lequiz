<?php

	require_once('config.php');

	function load_options() {
		global $filename_meta;
		global $remote_push_url;
		global $remote_push;

		if (file_exists ($filename_meta)) {
			$file_content = @file_get_contents($filename_meta);
			if($file_content === false) {
				$response = array (
					"status" => 403,
					"message" =>  "error reading metastate"
				);
			} else {
				$saved_state = @json_decode($file_content, true);
				if($saved_state === NULL) {
					$response = array (
						"status" => 403,
						"message" =>  "JSON decode error"
					);
				} else {
					$response = array (
						"status" => 200,
						"classes" => $saved_state['classes'],
						"levels" => $saved_state['levels'],
						"remote_push" => $remote_push,
						"remote_push_url" => $remote_push_url
					);
				}
			}
			echo json_encode ($response);
		} else {
			$response = array (
				"status" => 403,
				"message" =>  "metastate file does not exist"
			);
			echo json_encode ($response);
		}
	}

	function load_state() {
		global $filename;
		
		if (file_exists ($filename)) {
			$file_content = @file_get_contents($filename);
			if($file_content === false) {
				$response = array (
					"status" => 403,
					"message" =>  "error reading state"
				);
			} else {
				$saved_state = @json_decode($file_content, true);
				if($saved_state === NULL) {
					$response = array (
						"status" => 403,
						"message" =>  "JSON decode error"
					);
				} else {
					$response = array (
						"status" => 200,
						"questions" => $saved_state['questions'],
						"teams" => $saved_state['teams']
					);
				}
			}
			echo json_encode ($response);
		} else {
			$response = array (
				"status" => 403,
				"message" =>  "state file does not exist"
			);
			echo json_encode ($response);
		}
	}

	function save_state() {
		global $filename;

		if (!isset($_POST['teams']) || !isset($_POST['questions'])) {
			$response = array (
				"status" => 403,
				"message" => "error reading client state"
			);
			echo json_encode($response);
		} else {
			$new_saved_status = array (
				"questions" => json_decode($_POST['questions']),
				"teams" => json_decode($_POST['teams'])
			);	
			$ret_val = @file_put_contents($filename, json_encode($new_saved_status, JSON_PRETTY_PRINT));
			if ($ret_val === false) {
				$response = array (
					"status" => 500,
					"message" => "error writing state"
				);
			} else {
				$response = array (
					"status" => 200,
					"message" => "state saved"
				);
			}
			echo json_encode($response);
		}
	}

	$action = "none";
	if (isset ($_GET['action'])) {
		$action = $_GET['action'];
	}
	
	switch($action) {
		case "options":
			load_options();
			break;
		case "save":
			save_state();
			break;
		case "load":
			load_state();
			break;
		default:
			$response = array (
				"status" => 404,
				"message" => "action not found"
			);
			echo json_encode($response);
	}
?>