<? $office = ($_REQUEST["office"]) ? $_REQUEST["office"] : "BS_E13"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Regus</title>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<link rel="stylesheet" href="http://dev.jquery.com/view/tags/ui/latest/themes/flora/flora.all.css" type="text/css" media="screen" title="Flora (Default)">
	<script type="text/javascript" src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.core.js"></script>
	<script type="text/javascript" src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.slider.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$.post('control.php', {
				username: '<?=$office?>',
				control: 'none'
				}, function(xml) {
					alert(xml);
					light_start = $("light",xml).text();
					window_start = $("light",xml).text();
					$('#temperature').text($("temperature",xml).text()+'°');
					$('#ventilation').text($("ventilation",xml).text()+'°');
					$('#username').text($("username",xml).text());
					$('#light-loader').hide();
					$('#window-loader').hide();

					$('#light').slider({
		
						handle: 'light-handle',
						minValue: 1, 
						maxValue: 10,
						startValue: light_start,
						steps: 10,        
						range: false, 
						change: function(e,ui) { 
							set_value('light',$('#light').slider('value', 0)); 		       
						}
					}); 
		
					$('#window').slider({ 
						handle: 'window-handle',
						minValue: 1, 
						maxValue: 10,
						startValue: window_start,
						steps: 20,        
						range: false, 
						change: function(e,ui) { 
							set_value('window',$('#window').slider('value', 0));
						}
					});
				}
			);		

		});

		function set_value(control,value) {
			$.post('control.php', {
				username: '<?=$office?>',
				control: control,
				value: value
				}
			);		
		};

		function update_values() {
			$.post('control.php', {
				username: '<?=$office?>',
				control: 'none'
				}, function(xml) {
					$('#light').slider('moveTo', $("light",xml).text());
					$('#window').slider('moveTo', $("window",xml).text());
				}
			);		
		};
	</script>
	<style type="text/css">
		* {
			font-family:verdana;
		}
		.loader {
			background-image:url(ajax-loader.gif);
			background-position: top left;
			background-repeat:no-repeat;
			height:16px;
			padding-left:20px;
		}
	</style>
</head>
<body>

<h1>Regus kontrol for <?=$office?></h1>

<h2>Kontor</h2>
<a href="?office=BS_E13">BS_E13</a> 
<a href="?office=BS_C9">BS_C9</a> 
<a href="?office=BS_F1">BS_F1</a>

<h2>Lys</h2>
<div id="light-loader" class="loader">Henter status</div>
<div id='light' class='ui-slider-2' style="margin:10px;">
	<div class='light-handle'></div>	
</div>

<h2>Gardin</h2>
<div id="window-loader" class="loader">Henter status</div>
<div id='window' class='ui-slider-2' style="margin:10px;">
	<div class='window-handle'></div>	
</div>
<h2>Information</h2>
Temperatur: <span id="temperature"></span><br />
Ventilation: <span id="ventilation"></span><br />
Office: <span id="username"></span><br />


</body>
</html>
