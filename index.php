<?php
# Error reporting for all.
error_reporting (E_ALL);
ini_set ('display_errors', 1);

require_once("class.regus.php");

$officeid = (isset($_POST["officeid"]) ? $_POST["officeid"] : "BS_E13");

$regus = new Regus();

if (!$regus->init($officeid,$officeid)) {
	die($regus->last_error());
} else {

	if (isset($_POST["submit"])) {
		$regus->set_light($_POST["light"]);
		$regus->set_window($_POST["window"]);
	}
	$status = $regus->get_status();
	$light = intval(round($status["light"],-1));
	$window = intval(round($status["window"],-1));

	$status_all = $regus->get_status_all();

	$regus->close();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Regus</title>
</head>
<body>

<form action="" id="office" name="office" method="post">
<h1>Regus kontrol</h1>

<h2>Kontor</h2>
<input type="radio" name="office" value="BS_E13"<?=(($officeid=="BS_E13") ? " checked" : "")?> \>BS_E13<br />
<input type="radio" name="office" value="BS_C9"<?=(($officeid=="BS_C9") ? " checked" : "")?> \>BS_C9<br />

<h2>Lys</h2>
<?
/*
for ($i = 0; $i <= 10; $i++) {
	echo "<input type=\"radio\" name=\"light2\" value=\"".($i*10)."\">";#" value=\"".($i*10)."\"".(($light==intval($i*10)) ? "checked=\"checked\"" : "").">".($i*10)."&nbsp;\n";
}
*/
?>
<input type="text" name="light" value="<?=round($status["light"])?>" size="2">

<h2>Gardin</h2>
<input type="text" name="window" value="<?=round($status["window"])?>" size="2">

<h2>Information</h2>
Temperatur: <?=$status["temperature"]?><br />
Ventilation: <?=$status["ventilation"]?><br />
Aktivitet: <?=$status["activity"]?><br />
Office: <?=$status["username"]?><br />

<input type="submit" name="submit" value="Gem ændringer">

</form>
<?
echo "<pre>";
print_r($status_all);
echo "</pre>";
?>


</body>
</html>
