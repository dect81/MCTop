<?php

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

$debug = 0;

class debug {

	function include_report($file)
	{
		echo "$file подключен<hr/>";
	}
	
}

require("classes/base.php");
require("classes/in_out.php");
require("classes/join_edit.php");
require("classes/timer.php");
require("classes/graphics.php");


		//debug::include_report();






?>
