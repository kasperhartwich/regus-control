<?php
# Error reporting for all.
error_reporting (E_ALL);
ini_set ('display_errors', 1);
require_once("class.regus.php");

$office = $_REQUEST["username"];
$regus = new Regus();
if (!$regus->init($office,$office)) {
	echo $regus->last_error();
} else {

	switch ($_REQUEST["control"]) {
		case 'light':
			$regus->set_light($_REQUEST["value"]);
			break;
		case 'window':
			$regus->set_window($_REQUEST["value"]);
			break;
		default:
			break;
	}
	$status = $regus->get_status();
	$status["light"] = round($status["light"],-1);
	$status["window"] = round($status["window"],-1);
	echo $regus->response2xml($status);
}

?>