<?php
# Error reporting for all.
error_reporting (E_ALL);
ini_set ('display_errors', 1);

require_once("class.regus.php");
echo "<pre>";

#$username = "BS_C9";
$username = "BS_E13";
$regus = new Regus();
if (!$regus->init($username,$username)) {
	echo $regus->last_error();
} else {
#	var_dump($regus->set_temperature(-2));
#	var_dump($regus->set_light(60));
#	var_dump($regus->set_window(30));
#	var_dump($regus->get_cookiejar());
	var_dump($regus->get_status());
	print_r($regus->errors());
}
#echo $regus->close();


die();
/*
	[op_h] => 8,0					Højde på lokale til oversigtsbillede
    [op_hDT] => R
    [op_hRW] => R
    [op_w] => 16,0					Bredde på lokale til oversigtsbillede.
    [op_wDT] => R
    [op_wRW] => R
    [op_x] => 690,0					x-koordinat på lokale til oversigtsbillede
    [op_xDT] => R
    [op_xRW] => R
    [op_y] => 50,0					y-koordinat på lokale til oversigtsbillede
    [op_yDT] => R
    [op_yRW] => R
    [CUR2_value] => 100,0 %25		Vindue
    [CUR2_valueDT] => R
    [CUR2_valueRW] => R
    [HEA_value] => 99.6,0 %25		Radioator?
    [HEA_valueDT] => R
    [HEA_valueRW] => R
    [LYS2_state] => 0,0  			Lys tændt/slukket
    [LYS2_stateDT] => R
    [LYS2_stateRW] => R
    [LYS2_value] => 0,0 %25			Lys styrke
    [LYS2_valueDT] => R
    [LYS2_valueRW] => R
    [Pir_value] => 1,0 %25			Aktivitet?
    [Pir_valueDT] => R
    [Pir_valueRW] => R
    [TMA_value] => 21,0 °C			Temperatur indenfor
    [TMA_valueDT] => R
    [TMA_valueRW] => R
    [TMF_value] => 3,0 °C			Diff på aktuel rum temperatur og indstillet/bestilt temperatur
    [TMF_valueDT] => R
    [TMF_valueRW] => W
    [VAV_value] => 0,0 %25			Ventilation
    [VAV_valueDT] => R
    [VAV_valueRW] => R
*/

?>