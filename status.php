<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

$id = intval($_GET['id']);
$style = intval($_GET['style']);
if(isset($_GET['id']))
{

$query = "SELECT  status  FROM  rtt_servers_real WHERE `id` = $id  ";  
$result = $DB->query($query, __FILE__, __LINE__)  or  die(mysql_error()); 

while($info = mysql_fetch_array($result))  
{
	$status = $info['status'];
	break;
}
	

$pref = 'images/status/';
$style ='_'.$style;

header('Content-Type: image/gif');

if ($status == 1) {
	$pref .= 'on';
	}
elseif ($status == 0) {
	$pref .= 'off';
	}
elseif ($status == 2) {
	$pref .= 'check';
}

@include($pref.$style.'.gif');
}
else echo 'hacking attempt';
exit();
?>