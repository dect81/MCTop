<?php

class go_to_vote extends base {
	function go_to_vote() {
		$back=$_COOKIE['r_page'];
		#if(empty($_SESSION["rtt_playerCp_login"])){
			header("Location: http://mctop.su/rating/vote/$back");
		#}
		#else {
	#		header("Location: http://mctop.su/playerCp");
#			$_SESSION["rtt_playerCp_login"] = "0";
#		}
	}
}
?>
