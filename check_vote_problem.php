<?php

class check_vote_problem extends base {

	  function check_vote_problem() {
		global $CONF, $DB, $FORM, $LNG, $TMPL;
			if (isset($FORM['u'])) {
				$username = $DB->escape($FORM['u'], 1);
			} 
			if (isset($_COOKIE['uid'])) {

				$uid=$_COOKIE['uid'];	
				$uid=$this->clear_var($uid);	
			}
			if (isset($_COOKIE['r_vote'])) {
				$hash=$_COOKIE['r_vote'];	
				$hash=$this->clear_var($hash);	
			} else {
					header("Location: http://mctop.su");
			}
		if (isset($_COOKIE['uid'])&&isset($_COOKIE['r_vote'])&&$_COOKIE['r_vote']!="1"&&$_COOKIE['r_vote']!=""&&$_COOKIE['r_vote']!="<html xmlns=\"http://www.w3.org/1999/xhtml\">"){
		$row=$DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_votes WHERE hash='$hash' AND vk_id='$uid'  LIMIT 1",__FILE__, __LINE__);
		$helper=mysql_query("SELECT * FROM rtt_votes WHERE hash='$hash' AND vk_id='$uid'  LIMIT 1");
		$helpme = mysql_num_rows ($helper);
		if($helpme == 0) {

			if(isset($_GET['mode'])) {
				$mode = $_GET['mode'];
				$mode = $this->clear_var($mode);
			} 

				if($mode == 1)
				{
					$LNG['error_user_with_broken_cookie'] = "<div class='message message_notice'><p><span>Уведомление</span> Подтвердите исправление ошибки, для подтверждения перейдите по ссылке <a href='http://mctop.su/check_vote_problem/{$username}/2'>http://mctop.su/check_vote_problem/{$username}/2</a></p></div>";
					$TMPL['content'] .= $LNG['error_user_with_broken_cookie'];
				}
				if($mode == 2)
				{
					setcookie('r_vote','1', time()-(60*60*24*30),'/');					
					$LNG['error_user_with_broken_cookie'] = "<div class='message message_notice'><p><span>Уведомление</span>Ошибка была исправлена</a></p></div>";
					$TMPL['content'] .= $LNG['error_user_with_broken_cookie'];
				}
			} else {
				header("Location: http://mctop.su");
			}
		}

	}
}
?>
