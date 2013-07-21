<?php

class to_server extends base {
	  function to_server() {
		global $CONF, $DB, $FORM, $LNG, $TMPL;

			$id=intval($_GET['id']);
			if(isset($id)) {
				$row=$DB->fetch("SELECT `id`, `url`, `username` FROM {$CONF['sql_prefix']}_servers WHERE id='$id' LIMIT 1", __FILE__, __LINE__);
				if($row['url'])
				{
					session_start();
					if(!isset($_SESSION["site_project_{$id}"]) or (empty($_SESSION["site_project_{$id}"]))){
					$day = date('j');
					$_SESSION["site_project_{$id}"] = "1";
					$res=$DB->query("UPDATE `rtt_graphics_data` SET `day_{$day}_clicks` = `day_{$day}_clicks` +1  WHERE `username`='{$row['username']}'", __FILE__, __LINE__);
					$res=$DB->query("UPDATE {$CONF['sql_prefix']}_stats SET clicks=clicks+1 WHERE id='$id' LIMIT 1", __FILE__, __LINE__);
					}
					header("Location: ".$row['url']);
				} else {
					$TMPL['content'] = $this->do_skin('adv_error');
				}
			} else {
				$TMPL['content'] = $this->do_skin('adv_error');
			}

	}
}
?>
