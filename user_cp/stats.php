<?php

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class stats extends join_edit {
  function stats() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['user_cp_header'];
	$username = $TMPL['username'];
      $this->form();

}
  function form() {
    global $CONF, $DB, $LNG, $TMPL;

list($advertiser, $id) = $DB->fetch("SELECT advertiser, id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);

/*foreach($TMPL as $key=>$num) 
echo "\$TMPL[".$key."] = ".$num."<br>";*/ 
    if (!$advertiser) 
	{
      $row = $DB->fetch("SELECT join_date, active, ban_reason, attemps, success FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
	  $row2 = $DB->fetch("SELECT score, votes_all, votes_month, views, clicks FROM {$CONF['sql_prefix']}_stats WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row, $row2);
    }
	
      $attemps = $TMPL['attemps'];
      $success = $TMPL['success'];
  if($attemps <> 0)
  {
	$uptime = $success/$attemps*100*100;
	$uptime = ((int)($uptime)/100.0);
  }
  else
  {
      $uptime = 0;
  }
  
$TMPL['uptime'] = $uptime;
$timestamp = time();
$time_st = strftime('%H',$timestamp);

#Делаем динамическое приветствие для юзера
if ($time_st <6 or $time_st >= 22 )
{
$TMPL['time_st'] = 'Доброй ночи';
}
elseif($time_st <= 22 and $time_st >= 17 )
{
$TMPL['time_st'] = 'Добрый вечер';
}
elseif($time_st <=17 and $time_st >= 12 )
{
$TMPL['time_st'] = 'Добрый день';
}
elseif($time_st <=12 and $time_st >= 6 )
{
$TMPL['time_st'] = 'Доброе утро';
}
$lastlogin = time() - (24*60*60*7);
$position = mysql_query("set @i:=0;") or die(mysql_error());
$position = mysql_query("select *,@i:=@i+1 as number from rtt_stats stats, rtt_servers servers WHERE servers.id = stats.id AND active = 1 and topcraft <> 1 AND (servers.success/servers.attemps*100) > 50  order by `score` desc") or die(mysql_error());
//echo "<!--select *,@i:=@i+1 as number from rtt_stats stats, rtt_servers servers WHERE servers.id = stats.id AND active = 1 and topcraft <> 1 AND `lastlogin`>'$lastlogin' AND (servers.success/servers.attemps*100) > 30  order by `score` desc-->";
	while($info = mysql_fetch_array($position))  
	{
			
		if($id == $info['id'])
		{
		$real_position = $info['number'];
		//echo '1';
		break;
		}
	}
$TMPL['position'] = $real_position;
//echo $real_position;

//if($TMPL['active']== 3){$TMPL['user_cp_content'] = $this->do_skin('user_cp_banned');}

if (!$advertiser) {
  if($TMPL['active']==1) {
	$TMPL['active'] = "Активен";
	}  else { 
	if($uptime < 30 || empty($real_position)) {
	    $TMPL['position'] = 'Сервер не отображается в рейтинге';
		$TMPL['active'] = "<span class='jQtooltip' title='Ваш аккаунт неактивен. Аккаунт будет активирован, после того как Uptime сервера будет больше 30%, и после того как модераторы проверят сервер'>Неактивен</span>";
	} else{
	$TMPL['active'] = "<span class='jQtooltip' title='При последней проверке Ваш сервер был недоступен. Ваш сервер был перенесен в неактивные. При следующей проверке аккаунт будет активирован.'>Неактивен</span>";
	}
}	
	$TMPL['user_cp_content'] = $this->do_skin('user_cp_stats');
}

	else {
$TMPL['error_text'] = 'Данная функция доступна только для администраторов серверов.';
$TMPL['user_cp_content'] = $this->do_skin('adv_error');
	}

  }

       
}

?>