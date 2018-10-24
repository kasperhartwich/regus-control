<?php
function response2array($response) {
	$responsearray = explode("&&",$response);
	foreach ($responsearray as $var) {
		$temp = explode("=",$var);
		if ($temp[0]) {$info[$temp[0]] = $temp[1];}
	}
	return $info;
}

function regus_init ($username,$password) {
	global $ch;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1");
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "regusjar");
	
	curl_setopt($ch, CURLOPT_POSTFIELDS, "login=".$username."&B1.x=53&B1.y=10&B1=Logga+in&pwd=".$password);
	curl_setopt($ch, CURLOPT_URL, "http://90.152.73.234/sjsbroen_web/EXO4web/Login/extValidering.asp");
	$html = curl_exec($ch);
	
	if (!$html) {
		echo "<br />_cURL error number:" .curl_errno($ch);
		echo "<br />cURL error:" . curl_error($ch);
		exit;
	}
}

function regus($vars) {
	global $ch;
	if ($ch) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, "");
		curl_setopt($ch, CURLOPT_URL, "http://90.152.73.234/sjsbroen_web/exo4web/webpages/extFlashvalues.asp?".$vars);
		$curl = curl_exec($ch);
		if (!$curl) {
			echo "<br />cURL error number:" .curl_errno($ch);
			echo "<br />cURL error:" . curl_error($ch);
			exit;
		} else {
			return response2array($curl);
		}	
	} else {
		die("Not init!");
	}
}

function response_xml($array) {
	header('Content-type: text/xml'); 
	$xml .= "    <regus>\n";
	foreach ($array as $key => $value) {
	    $xml .= "        <{$key}>".utf8_encode($value)."</{$key}>\n";
	}     
	$xml .= "    </regus>\n";
    return $xml;
}

function regus_info() {
	$data = regus("level=M&module=UC1_3/74&commonmodule=CommonVariables5&action=read&varnameprefix=BS_E13_");
	
	$temp = explode(",",$data["CUR2_value"]);
	$info["gardin"] = intval(round($temp[0],-1));
	
	$temp = explode(",",$data["LYS2_value"]);
	$info["lys"] = intval(round($temp[0],-1));
	
	$temp = explode(",",$data["VAV_value"]);
	$info["ventilation"] = round($temp[0],1);
	
	$temp = explode(",",$data["TMA_value"]);
	$info["rum"] = round($temp[0],1);
	
	$temp = explode(",",$data["HEA_value"]);
	$info["radiator"] = round($temp[0],1);
	
	return $info;
}

?>