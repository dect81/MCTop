<?php
if($debug) {
	$file = __FILE__;
	debug::include_report($file);
}

class checkers {

	function check_position () {
	global $DB;

	$i = 0;
	//$con = mysql_connect('localhost', 'hoster1129', '159631') or die(mysql_error());
	$time = time();
	$start = microtime(1);
	$check_delay = 3*60;
	//mysql_select_db('hoster1129', $con) or die(mysql_error());
	$older_check = $time - $check_delay;


	$list = $DB->query("set @i:=0;", __FILE__, __LINE__) or die(mysql_error());
	$list = $DB->query("select *,@i:=@i+1 as number from rtt_stats stats, rtt_servers servers where  servers.id = stats.id AND active = 1 AND (servers.success/servers.attemps*100) > 30 order by `score` DESC", __FILE__, __LINE__) or die(mysql_error());
	$day = date('j');
		while($info = mysql_fetch_array($list))  
		{
			$position = $info['number'];
			$bd_position = $info['position']; //для проверки - первый раз ли чекаем
			$username = $info['username'];
			$position_check = $info['position_check'];
			$direction = $info['direction'];
			$position_changed = $info['position_changed'];
			
			if($direction == 0 && $position_check == 0 && $position_changed == 0 & $bd_position == 0 ) {
				$q = "UPDATE `rtt_stats` SET `position_changed` = '{$position_changed}', `direction` = {$direction}, `position_check`='1', `position`='{$position}'  WHERE `username`='{$username}'";	
				echo "$q<br/>";
				$DB->query($q, __FILE__, __LINE__);	
			} else {
				if($position_check == 0) {
					if($position == $bd_position)
					{
						$direction = 0; //0 - все ок - остались как были
						$position_changed = 0;
					} else {			
						if($position < $bd_position )
						{
							$direction = 1; //1 - поднялись в рейтинге - хорошо
							$position_changed = $bd_position - $position; 
						} else {
							$direction = 2; //2 - опустились в рейтинге - потеряли позиции
							$position_changed = $position - $bd_position; 
						}
					}
					$q = "UPDATE `rtt_stats` SET `position_changed` = '{$position_changed}', `direction` = {$direction}, `position_check`='1', `position`='{$position}'  WHERE `username`='{$username}'";	
					$q2 = "UPDATE `rtt_graphics_data` SET `day_{$day}_position` = '{$position}'  WHERE `username`='{$username}'";	
					echo "$q2<br/>";

					$DB->query($q, __FILE__, __LINE__);
					$DB->query($q2, __FILE__, __LINE__);
				}
			
			
			}
		} 

	$work = microtime(1) - $start;
	}
}

?>