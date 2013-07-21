<?php
class rate extends base {
  function rate() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['rate_header'];
	$ip=$_SERVER['REMOTE_ADDR'];
	 if (isset($FORM['u']) && $FORM['u']) {
	      $TMPL['username'] = $DB->escape($FORM['u'], 1);
	}
	if (isset($_COOKIE['uid'])) {
		$uid=$_COOKIE['uid'];	
	}

if (isset($_COOKIE['uid'])&&isset($_COOKIE['r_vote'])&&$_COOKIE['r_vote']!="1"&&$_COOKIE['r_vote']!=""&&$_COOKIE['r_vote']!="<html xmlns=\"http://www.w3.org/1999/xhtml\">")
{

		$hash=$_COOKIE['r_vote'];
		$row=$DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_votes WHERE hash='$hash' AND vk_id='$uid'  LIMIT 1",__FILE__, __LINE__);
		$helper=mysql_query("SELECT * FROM rtt_votes WHERE hash='$hash' AND vk_id='$uid'  LIMIT 1");
		$helpme = mysql_num_rows ($helper);
		if($helpme == 0) {
			$username = $TMPL['username'];
			$LNG['error_user_with_broken_cookie'] = "<div class='message message_notice'><p><span>Уведомление</span> Если Вы имеете проблему, возникшую в процессе голосования, перейдите по следующей ссылке: <a href='http://mctop.su/check_vote_problem/{$username}/1'>http://mctop.su/check_vote_problem/{$username}/1</a></p></div>";
			$TMPL['content'] .= $LNG['error_user_with_broken_cookie'];
		}
		$time = time();
		
		if(isset($row['username'])&&$row['hash']==$hash){
		
			if ($row['username']==$TMPL['username'])
			{

				if($row['time']<$time)
				{

					if (isset($FORM['u']) && $FORM['u']) {
						$TMPL['username'] = $DB->escape($FORM['u'], 1);

						$ip = $DB->escape($_SERVER['REMOTE_ADDR'], 1);

						if (!isset($FORM['rating'])) {
							$this->form_vote();
						}
						else {
							$this->process($ip, $hash);
						}    

					}
				} else	{
				$this->form_wait();
				}
			} else {
			$this->form_another();
			}
	}/*

	*/
} else {
	if (isset($_COOKIE['uid'])&&isset($_COOKIE['pass']))
	{
		$hash = md5(time() + rand(1, 5000));
		$timenow=(time()+(3600*24));
		$res = $DB->fetch("SELECT `id`, `time`, `hash`,`username` FROM {$CONF['sql_prefix']}_votes  WHERE vk_id='$uid' AND vote='0' order by time desc LIMIT 1", __FILE__, __LINE__);
		if($res['id'])
		{
			setcookie('r_vote', $res['hash'], $res['time']+60*60*24*30,'/');

			if ($res['username']==$TMPL['username'])
			{
				$this->form_wait();
			} else {
				$this->form_another();
			}
		} else {
			$res = $DB->query("INSERT INTO {$CONF['sql_prefix']}_votes (vk_id, username, ip, hash, time) VALUES ('$uid', '{$TMPL['username']}', '$ip', '$hash', '$timenow')", __FILE__, __LINE__);     
			setcookie('r_vote', $hash, time()+60*60*24*30,'/');
			setcookie('popup', $timenow, time()+60*60*24*30,'/');
			$this->form_set();
		}
	}
		
	else
	{
		$this->form_not_vk();
	}
}

  }

  function form_vote() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	$username = $TMPL['username'];
    $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
    $TMPL = array_merge($TMPL, $row);
    $link = "<a href=\"{$TMPL['url']}\" onclick=\"out('{$TMPL['username']}');\">{$TMPL['title']}</a>";
    $TMPL['rate_message'] = sprintf($LNG['rate_message'], $link);
	list($give_bonus, $secret_word, $script_url) = $DB->fetch("SELECT give_bonus, secret_word, script_url FROM rtt_servers where username = '{$username}'", __FILE__, __LINE__);

	//if (($give_bonus == 1) and (!empty($secret_word)) and (!empty($script_url))) {

		//$bonus = file_get_contents("{$script_url}?player={$player}&hash={$hash}", '1');
		//echo "$bonus";
	//}
    $TMPL['content'] = $this->do_skin('rate_form_vote');
  }
  
    function form_wait() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	$hash=$_COOKIE['r_vote'];
	$vk = $_COOKIE['uid'];
	$row=$DB->fetch("SELECT `time`, `username` FROM {$CONF['sql_prefix']}_votes WHERE hash='$hash' LIMIT 1",__FILE__, __LINE__);
	$time = $row['time'];
	$time = $time + 3600;
	if(isset($_COOKIE['r_vote']) and isset($_COOKIE['uid']) and $time == '14400' or empty($time)) {
	  header("Location: {$_SERVER['HTTP_REFERER']}");
	}
	
	$time = date('Y/m/d H:i', $time);
	$TMPL['time_user'] = $time;


    $TMPL['content'] = $this->do_skin('rate_form_wait');
  }
  
      function form_another() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $hash=$_COOKIE['r_vote'];
	$voter = $TMPL['username'];
	list($voter_id)=$DB->fetch("SELECT `id` FROM {$CONF['sql_prefix']}_servers WHERE username='$voter' LIMIT 1",__FILE__, __LINE__);
	list($username)=$DB->fetch("SELECT `username` FROM {$CONF['sql_prefix']}_votes WHERE hash='$hash' LIMIT 1",__FILE__, __LINE__);
	list($title, $id)=$DB->fetch("SELECT `title`, `id` FROM {$CONF['sql_prefix']}_servers WHERE username='$username' LIMIT 1",__FILE__, __LINE__);

	$TMPL['username'] = $username;
	$TMPL['title'] = $title;
	$TMPL['id'] = $id;
    $TMPL['content'] = $this->do_skin('rate_form_another');
  }
  
      function form_not_vk() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	$TMPL['url'] = $_SERVER['REQUEST_URI'];
	$TMPL['username'] = $DB->escape($FORM['u'], 1);
	$username = $TMPL['username'];
	setcookie('r_page',"$username",time()+60*60*24*60,'/');


	list($title, $id)=$DB->fetch("SELECT `title`, `id` FROM {$CONF['sql_prefix']}_servers WHERE username='$username' LIMIT 1",__FILE__, __LINE__);
	$TMPL['title']=$title;
	$TMPL['id']=$id;
    $TMPL['content'] = $this->do_skin('rate_form_not_vk');
  }
  
    function form_set() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;


    $TMPL['content'] = $this->do_skin('rate_form_set');
  }
  

  function process($ip, $hash) {
    global $CONF, $DB, $FORM, $TMPL;

    // Review
	$uid=$_COOKIE['uid'];
	$uid=intval($uid);
    if (isset($FORM['review_check'])&&($FORM['review_check']==1)) {
		$date = date("Y-m-d H:i:s", time() + (3600*$CONF['time_offset']));
		list($id) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_reviews", __FILE__, __LINE__);
		if (!$id) {
		$id = 1;
		}

		
		$review = strip_tags($FORM['review']);
		$review = nl2br($review);
		$review = $this->bad_words($review);
		$nickname = strip_tags($FORM['nickname']);
		
		$TMPL['review'] = $review;

		$review = $DB->escape($review);

		$DB->query("INSERT INTO {$CONF['sql_prefix']}_reviews (vk_id, username, id, date, review, active) VALUES ('$uid','{$TMPL['username']}', {$id}, '{$date}', '{$review}', {$CONF['active_default_review']})", __FILE__, __LINE__);

 }

	list($vote, $time)=$DB->fetch("SELECT `vote`, `time` FROM {$CONF['sql_prefix']}_votes WHERE hash='$hash' order by time desc  LIMIT 1", __FILE__, __LINE__);
	if($vote==0)
	{
	    // Rating
    $rating = intval($FORM['rating']);
    if ($rating > 5) {
      $rating = 5;
    }
    elseif ($rating < 1) {
      $rating = 1;
    }
	$nickname = $FORM['nickname'];
	$month = mysql_fetch_row(mysql_query("SELECT last_new_month from rtt_etc"));
	$month = $month[0];
	
    $votes_month = $DB->query("SELECT id FROM {$CONF['sql_prefix']}_votes WHERE username = '{$TMPL['username']}' and vote = 1 and month = '{$month}'", __FILE__, __LINE__);
    $votes_all = $DB->query("SELECT id FROM {$CONF['sql_prefix']}_votes WHERE username = '{$TMPL['username']}' and vote = 1", __FILE__, __LINE__);
	$votes_month++;
	$score = (($votes_all/4)+(15*$votes_month));

	
    $DB->query("UPDATE {$CONF['sql_prefix']}_stats SET votes_all = votes_all + 1, votes_month = '{$votes_month}', score = '{$score}' WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__) or die(mysql_error());
    $DB->query("UPDATE {$CONF['sql_prefix']}_votes SET vote='1', mark='{$rating}', month = '{$month}', nickname ='{$nickname}' WHERE username = '{$TMPL['username']}' AND hash='$hash' ", __FILE__, __LINE__);
	$username = $TMPL['username'];
	list($give_bonus, $secret_word, $script_url) = $DB->fetch("SELECT give_bonus, secret_word, script_url FROM rtt_servers where username = '{$username}'", __FILE__, __LINE__);

	if (($give_bonus == 1) and (!empty($secret_word)) and (!empty($script_url))) {
		$hash = md5($secret_word.$nickname);
		$bonus = file_get_contents("", '1');
		$ch = curl_init ();
		curl_setopt ( $ch , CURLOPT_URL , "{$script_url}?player={$nickname}&hash={$hash}" );
		curl_setopt ( $ch , CURLOPT_HEADER , 0 );	
		$tmp = curl_exec ( $ch );	
		curl_close ( $ch );
		//$player = 'Medvedkoo';
		//$hash = md5($secret_word.$player);
		//echo "<!-- $give_bonus {$script_url}?player={$player}&hash={$hash} -->";
		//$bonus = file_get_contents("{$script_url}?player={$nickname}&hash={$hash}", '1');
		//echo "$bonus";
	}
	//$TMPL['content'] .= '1';
	setcookie('r_vote','1', time()+60*60*24*30,'/');
	$TMPL['content'] = $this->do_skin('rate_finish');
	}
	else
	{
    $TMPL['content'] = $this->do_skin('rate_finish_earlier');
	setcookie('r_vote','1', time()+60*60*24*30,'/');
	}
  }
}
?>
