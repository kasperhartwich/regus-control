<?php
# Error reporting for all.
error_reporting (E_ALL);
ini_set ('display_errors', 1);
require_once("class.regus.php");

$regus = new Regus();

$letters = array('A','B','C','D','E','F','G');
$offices = array();
foreach ($letters as $letter) {
	for ($i = 1; $i <= 20; $i++) {
		$offices[] = 'BS_'.$letter.$i;
	}	
}

foreach ($offices as $office) {
	if ($regus->init($office,$office)) {
		echo $office;
		echo "<br />";
	}
}

/*
	Bruted:
BS_A1
BS_A5
BS_A13
BS_B1
BS_B9
BS_B13
BS_C5
BS_C9
BS_D1
BS_D5
BS_D13
BS_E1
BS_E9
BS_E13
BS_F9
BS_F13
BS_G9
BS_G13
*/
?>