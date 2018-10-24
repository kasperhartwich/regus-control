<?php
//
//  Regus class for controlling rented rooms at Regus offices at Sluseholmen 2-4, Copenhagen, Denmark,
//  Created: 18th of August 2008 by Kasper Hartwich
//
//  Todo: log, logout
//
class Regus {
	
	var $curl_handle = false;
	var $errors = array();
	var $username;
	var $office_id;
	var $default_office_id;
	
	# This is the path to the Regus administration.
	var $url = 'http://90.152.73.234/sjsbroen_web/EXO4web';

	#Location of the cookiejar.
	var $cookiejar_path= "/tmp/";
			
	function init($username,$password,$office_id=false) {
		if (!$username) {
			$this->errors[] = "No username given.";
			return false;
			exit;
		}
		$this->username = $username;
		$this->curl_handle = curl_init();
		curl_setopt($this->curl_handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1");
		curl_setopt($this->curl_handle, CURLOPT_FAILONERROR, true);
#		if (!ini_get("safe_mode")) {curl_setopt($this->curl_handle, CURLOPT_FOLLOWLOCATION, true);} # Only with safe_mode disabled.
		curl_setopt($this->curl_handle, CURLOPT_AUTOREFERER, true);
		curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl_handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($this->curl_handle, CURLOPT_COOKIEJAR, $this->cookiejar_path."regus_".$username."_cookiejar");
		curl_setopt($this->curl_handle, CURLOPT_COOKIEFILE, $this->cookiejar_path."regus_".$username."_cookiejar");		

		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, "login=".$username."&B1.x=53&B1.y=10&B1=Logga+in&pwd=".$password);
		curl_setopt($this->curl_handle, CURLOPT_URL, $this->url."/Login/extValidering.asp");

		$curl_response = curl_exec($this->curl_handle);
	
		if ($curl_response) {
			if (stristr($curl_response, 'extHomePage.asp')) {

				if (!$office_id) {
					$this->office_id = $this->get_office_id();
				}				
				return true;				

			} elseif (stristr($curl_response, 'extFinish.asp')) {

				if (!$office_id) {
					$this->office_id = $this->get_office_id();
				}				
				return true;				

			} elseif (stristr($curl_response, 'extLogin.asp')) {
				$this->errors[] = "Login error (".$username."/".$password.")";
				return false;
			} else {
				$this->errors[] = "Unknown loginerror: ".$curl_response;
				return false;
			}


		} else {
			$this->errors[] = "Error initializing cURL ((".curl_errno($this->curl_handle).") ".curl_error($this->curl_handle).")";
			return false;
		}

	}
	
	function close() {
#		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, "");
#		curl_setopt($this->curl_handle, CURLOPT_URL, $this->url."extFinish.asp");
#		$curl_response = curl_exec($this->curl_handle);
		curl_close($this->curl_handle);
		$this->curl_handle = NULL;
#		return $curl_response;
		return true;
	}
		
	function set_light($value,$office_id=false) {
		if (!$office_id) {$office_id = $this->office_id;}
		if ($value>=0 || $value<=100) {
			if ($value==0) {
				$state=0;
				$value=1;
			} else {
				$state=1;
			}
			$current_status = $this->get_status($office_id);
/*			if (!$current_status["light_status"]) {
				if ($this->send_request("module=UC1_3/".$office_id."&action=write&flashvariabel=vpac2.".$this->username."_lys1&flashvalue=".$state)) {
					return true;
				} else {
					$this->errors[] = "Error setting new light state. (".$state.")";
					return false;
				}
			}
*/
				if ($this->send_request("module=UC1_3/".$office_id."&action=write&flashvariabel=vpac2.".$this->username."_lys1&flashvalue=".$state)) {
					return true;
				} else {-
					$this->errors[] = "Error setting new light state. (".$state.")";
					return false;
				}
			if ($this->send_request("module=UC1_3/".$office_id."&action=write&flashvariabel=vpac2.".$this->username."_lys2&flashvalue=".$value)) {
				return true;
			} else {
				$this->errors[] = "Error setting new light value. (".$value.")";
				return false;
			}
		} else {
			$this->errors[] = "Value ".$value." not allowed.";
			return false;
		}
	}

	function set_window($value,$office_id=false) {
		if (!$office_id) {$office_id = $this->office_id;}
		if ($value>=0 || $value<=100) {
			if ($value==0) {
				$state=0;
				$value=1;
			} else {
				$state=1;
			}
/*			if ($this->send_request("module=UC1_3/".$office_id."&action=write&flashvariabel=vpac2.".$this->username."_gar1&flashvalue=".$state)) {
				return true;
			} else {
				$this->errors[] = "Error setting new window state. (".$state.")";
				return false;
			}
*/			if ($this->send_request("module=UC1_3/".$office_id."&action=write&flashvariabel=vpac2.".$this->username."_gar2&flashvalue=".$value)) {
				return true;
			} else {
				$this->errors[] = "Error setting new window value. (".$value.")";
				return false;
			}
		} else {
			$this->errors[] = "Value ".$value." not allowed.";
			return false;
		}
	}
	
	function set_temperature($value,$office_id=false) {
		if (!$office_id) {$office_id = $this->office_id;}
		$value = round($value,1);
		if ($value<3 || $value>-3) {
			if ($this->send_request("module=UC1_3/".$office_id."&action=write&flashvariabel=pifa5_".$this->username."_tmf_value&flashvalue=".$value)) {
				return true;
			} else {
				$this->errors[] = "Error setting new light value.";
				return false;
			}
		} else {
			$this->errors[] = "Value ".$value." not allowed. Must be between 3 and -3.";
			return false;
		}
	}

	function get_status_all($office_id=false) {
		if (!$office_id) {$office_id = $this->office_id;}
		#level=M&module=UC1_3/74&commonmodule=CommonVariables5&action=read&varnameprefix=BS_E13_
		#level=M&module=uc1_6&action=read
		#level=M&module=uc1_1&action=read
		$response_inside = $this->send_request("level=M&module=UC1_3/".$office_id."&commonmodule=CommonVariables5&action=read&varnameprefix=".$this->username."_");
		$data_inside = $this->response2array($response_inside);
		$response_outside = $this->send_request("level=M&module=uc1_6&action=read");
		$data_outside = $this->response2array($response_outside);
		$response_system = $this->send_request("level=M&module=uc1_1&action=read");
		$data_system = $this->response2array($response_system);
		return array_merge($data_inside,$data_outside,$data_system);
	}
	
	function get_status($office_id=false) {
		if (!$office_id) {$office_id = $this->office_id;}
		$response = $this->send_request("level=M&module=UC1_3/".$office_id."&commonmodule=CommonVariables5&action=read&varnameprefix=".$this->username."_");
		$data = $this->response2array($response);
			/*
			[op_h] => 8,0					Højde på lokale til oversigtsbillede
		    [op_w] => 16,0					Bredde på lokale til oversigtsbillede.
		    [op_x] => 690,0					x-koordinat på lokale til oversigtsbillede
		    [op_y] => 50,0					y-koordinat på lokale til oversigtsbillede
		    [CUR2_value] => 100,0 %25		Vindue
		    [HEA_value] => 99.6,0 %25		Radioator?
		    [LYS2_state] => 0,0  			Lys tændt/slukket
		    [LYS2_value] => 0,0 %25			Lys styrke
		    [Pir_value] => 1,0 %25			Aktivitet?
		    [TMA_value] => 21,0 °C			Temperatur indenfor
		    [TMF_value] => 3,0 °C			Diff på aktuel rum temperatur og indstillet/bestilt temperatur
		    [VAV_value] => 0,0 %25			Ventilation
			*/
		if (is_array($data)) {
			if (sizeof($data)>0) {
				$temp = explode(",",$data["CUR2_value"]);
				$info["window"] = round($temp[0],1);
				
				$temp = explode(",",$data["LYS2_value"]);
				$info["light"] = round($temp[0],1);
				
				$temp = explode(",",$data["LYS2_state"]);
				$info["light_status"] = round($temp[0]);
	
				$temp = explode(",",$data["Pir_value"]);
				$info["activity"] = ($temp[0]==1) ? false : true;
	
				$temp = explode(",",$data["VAV_value"]);
				$info["ventilation"] = round($temp[0]);
				
				$temp = explode(",",$data["TMA_value"]);
				$info["temperature"] = round($temp[0],1);
				
				$temp = explode(",",$data["HEA_value"]);
				$info["radiator"] = round($temp[0],1);
	
				$info["username"] = $this->username;
	
				$info["office_id"] = $office_id;
	
				return $info;			
			} else {
				$this->errors[] = "No data received from system. Array empty.";
				return false;
			}
		} else {
			$this->errors[] = "No data received from system.";
			return false;
		}
	}

	function get_office_id() {
		// Get office_id
		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, "");
		curl_setopt($this->curl_handle, CURLOPT_URL, $this->url."/WebPages/extHomePage.asp");
		$curl_response = curl_exec($this->curl_handle);
	
		$output = array();
	
		if (eregi ("module=UC1_3/"."(.*)"."\"", $curl_response, $output)) {
			$this->default_office_id = $output[1];
			return $output[1];
		} else {
			$this->errors[] = "Could not get default office number.";
			return false;
		}
		
	}

	function send_request ($vars) {
		if ($this->curl_handle) {
			curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, "");
			curl_setopt($this->curl_handle, CURLOPT_URL, $this->url."/webpages/extFlashvalues.asp?".$vars);

			$curl_response = curl_exec($this->curl_handle);

			if ($curl_response) {
				return $curl_response;
			} else {
				$this->errors[] = "Error sending response thru cURL ((".curl_errno($this->curl_handle).") ".curl_error($this->curl_handle).")";
				return false;
			}

		} else {
			$this->errors[] = "Object not initialized";
			return false;
		}		
	}
	
	function response2array($response) {
		$info = array();
		$responsearray = explode("&&",$response);
		foreach ($responsearray as $var) {
			$temp = explode("=",$var);
			if ($temp[0]) {$info[$temp[0]] = $temp[1];}
		}
		return $info;
	}

	function response2xml($array) {
		$xml = "";
		header('Content-type: text/xml'); 
		$xml .= '<regus location="'.$this->username.'">';
		foreach ($array as $key => $value) {
		    $xml .= "<{$key}>".utf8_encode($value)."</{$key}>";
		}     
		$xml .= "</regus>";
	    return $xml;
	}
	
	function last_error() {
		return $this->errors[count($this->errors)-1];
	}

	function errors() {
		return $this->errors;
	}
}
?>