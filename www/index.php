<?php 
session_start();
// Current Station
if (!isset($_SESSION['s_name'])){ $_SESSION['s_name'] = 'unknown';}


function pandora_ctl($letter)
{ 
	$cmd = "echo ".$letter." > /tmp/pianobar";
	exec($cmd);
}

function hud()
{
	$audio_levels = exec('amixer get PCM');
	return $audio_levels;
}

// Change audio levels
function change_audio($level)
{
	$amt = $level[0].'dB'.$level[1];

	$levels = "amixer set PCM ".$amt;
	exec($levels);
}

function generate_stations()
{
	$stations = [];
	$file = "/tmp/pianobar_stations"; //Path to your *.txt file 
	$lines = file('/tmp/pianobar_stations');
	foreach ($lines as $line_num => $line) 
	{
		//echo "<a href='?station=".$line_num."'>".$line."</a><br />";
		array_push($stations, $line);
	}
	return $stations;
}

function current_station()
{
	$generate_stations = generate_stations();
	$station_index = 0;
	foreach($generate_stations as &$station)
	{
		echo "<a href='?station=".$station_index."'>".$station."</a><br />";

		if (isset($_SESSION['station']))
		{
			if($_SESSION['station'] == $station_index)
			{
				unset($_SESSION['s_name']);
				$_SESSION['s_name'] = $station; 
			} 
		}
		else {$_SESSION['s_name'] = 'unknown';}
		$station_index ++;
	}
}

if(isset($_POST['start']))
{
	if($_POST['start'] == 'Start' && $_POST['user'] != '')
	{
		$cmd = "/bin/bash /home/pi/pandora.sh " . $_POST['user'];
		//echo exec('tmp.sh');
		exec ($cmd);
		//echo $ouput;
	}
}
if(isset($_POST['stop']))
{
	if($_POST['stop'] == 'Stop')
	{
		$cmd = "echo q > /tmp/pianobar ";
		//echo exec('tmp.sh');
		exec ($cmd);
		$_SESSION['s_name'] = 'unknown';
		//echo $ouput;
	}
}

//User presets
if (isset($_SESSION['example_user']) && (isset($_POST['user'])))
{
	if ($_POST['user'] == 'example_user')
		{
			$_SESSION['example_user'] = 'selected';
		}
}
else { $_SESSION['example_user'] = '';}

if(isset($_GET['station']))
{
	pandora_ctl('s'.$_GET['station']);
	$url = strtok($_SERVER['REQUEST_URI'], '?');
	$_SESSION['station']=$_GET['station'];
}

if(isset($_POST['audio']))
{ 
	$e = $_POST['audio'];
	echo change_audio($e);
}

if(isset($_POST['pandora']))
{
	if($_POST['pandora'])
	{	
		$p = $_POST['pandora'];
		if ($p == 'Play/Pause')	{ $q = 'p'; }
		if ($p == 'Next') { $q = 'n'; }
		if ($p == '') { $q = ''; }
		echo pandora_ctl($q);
	}
}

if(isset($url))
{
        header( 'Location: '.$url);
}
?>
<html>
<head>
<style type='text/css'>
a:link {color:blue;}      /* unvisited link */
a:visited {color:blue;}  /* visited link */
a:active {color:red;}  /* selected link */
a {padding: 10px 0;}
</style>
</head>
<body style='text-align:center; width:auto;'>
<h1>It works!</h1>
<form method="post" style='float:left; margin: 0 0 0 200px;'>
<div style='text-align:center;'>
	<?=hud();?><br>
	<input type='submit' value='1+' name='audio'>
	<input type='submit' value='5+' name='audio'>
	<input type='submit' value='5-' name='audio'>
	<input type='submit' value='1-' name='audio'><br>
</div>
<input type='submit' value='Play/Pause' name='pandora'><input type='submit' value='Next' name='pandora'><br>
</form>

<div style='text-align:center; margin:auto auto auto 50px; padding: 5px; float:left; border: solid thin black; display:inline;'>
<h2 style='margin:0; text-align:center'>Pandora Stations</h2>
<form method='post'>
<select name='user'> 
	<option value='' selected=''>--</option>
	<option value='example_user' selected='<?=$_SESSION['example_user'];?>'>Example User</option>
</select>
<input type='submit' value='Start' name='start'><input type='submit' value='Stop' name='stop'>
</form>
<span style='color:red;'><?=$_SESSION['s_name'];?></span><br><br>
<div style='text-align:left;'><? current_station(); ?></div>
</div>
</body></html>
