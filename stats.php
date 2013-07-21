<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}
$TMPL['page_name_title'] = '';

$TMPL['sid'] = $_GET['id'];
class stats extends base {
  function stats() {
    global $FORM;

    if (isset($FORM['id'])) { $stats = new stats_site; }
    //else { $stats = new stats_overall; }
  }

 
}

class stats_site extends stats {
  function stats_site() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    include('sources/misc/cp.php');

    $TMPL['header'] = '';

    $TMPL['id'] = $DB->escape($FORM['id'], 1);
    list($check, $active) = $DB->fetch("SELECT id, active FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);

	if($active == 3) {
		$LNG['server_does_not_exist'] = "<br/></br><div class='message message_error'><p><span>Внимание!</span><br/> Ваш сервер нарушает пункт 1.5 <a href='http://mctop.su/rules'><b>правил</b></a> рейтинга<br/>После того как Вы уберете все счетчики и ссылки на TopCraft, напишите в <a href='http://mctop.su/cp/support'><b>техподдержку</b></a>, с просьбой о восстановлении</p></div>";
		$TMPL['content'] = $LNG['server_does_not_exist'];	
	} 
	elseif($active == 4) {
		$LNG['server_does_not_exist'] = "<br/></br><div class='message message_error'><p><span>Внимание!</span><br/> Ваш проект был ислючен из рейтинга</p></div>";
		$TMPL['content'] = $LNG['server_does_not_exist'];	
	}				
	elseif ($active == 1) {
	if($check){
    $server_info = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
	$username = $server_info['username'];
    $stats = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_stats WHERE username = '{$username}'", __FILE__, __LINE__);
	$project_id = $stats['id'];
	

	
    list($TMPL['project_id'],$TMPL['banner_url'], $TMPL['project_title']) = $DB->fetch("SELECT id, banner_url, title FROM rtt_servers WHERE username = '{$username}'", __FILE__, __LINE__);
	$TMPL = array_merge($TMPL, $stats, $server_info);
	
	$day = date('j');
    session_start();
	
	if(!isset($_SESSION["project_{$stats['id']}"]) or (empty($_SESSION["project_{$stats['id']}"]))){
		$_SESSION["project_{$stats['id']}"] = "1";
		$query = "UPDATE `rtt_graphics_data` SET day_{$day}_views = day_{$day}_views +1  WHERE `username`='{$TMPL['username']}'";
		$DB->query($query, __FILE__, __LINE__);
		$query = "UPDATE `rtt_stats` SET views = views + 1  WHERE `username`='{$TMPL['username']}'";
		$DB->query($query, __FILE__, __LINE__);
	} 
	

		
	$ip = $TMPL['ip'];
	$id = $TMPL['id'];
	$port = $TMPL['port'];
	if($port<>'25565') $TMPL['adress'] = $ip.':'.$port;
	else $TMPL['adress']=$ip;
	$attempts = $TMPL['attempts'];
	$success = $TMPL['success'];
	$username = $TMPL['username'];
	$status = $TMPL['status'];
	if($status!=0){
		$Server = new MinecraftStatus($IP = $ip, $Port = $port);
		$players = $Server->CurPlayers. ' / '.$Server->MaxPlayers;
		$TMPL['players'] = $players;	
	}
	

//if ($active==1) {
    if ($stats) {

  if($attempts <> 0)
  {
	$uptime = $success/$attempts*100*100;
	$uptime = ((int)($uptime)/100.0);
  }
  else
  {
  $uptime = 0;
  }    
  
		$TMPL['uptime'] = $uptime;

		$TMPL['banner'] = "<a href= '{$TMPL['list_url']}/rating/server/out/{$project_id}'><img  src='{$TMPL['banner_url']}'/></a>";
		$TMPL['url'] = "<a href='{$TMPL['list_url']}/rating/server/out/{$project_id}'> Перейти </a>";

		if ($status == 1) {
		$TMPL['status']="<img src='http://mctop.su/images/status/on_1.gif' border='0'/>";
		} elseif ($status == 0){
			$TMPL['status']="<img src='http://mctop.su/images/status/off_1.gif' border='0'/>";
		} elseif ($status == 2){
			$TMPL['status']="<img src='http://mctop.su/images/status/check_1.gif' border='0'/>";
		}
		
$id = $TMPL['id'];
list($TMPL['num_votes']) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_ip_log WHERE id = '$id'", __FILE__, __LINE__);
        $TMPL['category_url'] = urlencode($TMPL['category']);
        #-----------------------------------
        if($TMPL['whitelist'] ) $TMPL['whitelist'] = 'Включен';
        else $TMPL['whitelist'] = '<font color="red">Отключен</font>';
		#-----------------------------------
		if($TMPL['clienttype'] ) $TMPL['clienttype'] = 'Лицензия';
        else $TMPL['clienttype'] = '<font color="red">Пиратская копия</font>';
		#---------------------------------------
			if ($TMPL['server_type'] == 0) {
		$TMPL['server_type'] = 'Неизвестен';
	} elseif ($TMPL['server_type'] == 1) {
		$TMPL['server_type'] = 'Industrial';
	} elseif ($TMPL['server_type'] == 2) {
		$TMPL['server_type'] = 'Creative';
	} elseif ($TMPL['server_type'] == 3) {
		$TMPL['server_type'] = 'Survival';
	}	
      $TMPL['header'] .= "  {$TMPL['title']}";
      $TMPL['category_url'] = urlencode($TMPL['category']);
	  
      $res = $DB->query("UPDATE {$CONF['sql_prefix']}_stats SET views=views+1 WHERE id='$id' LIMIT 1", __FILE__, __LINE__);
	  
      $query = "SELECT id, date, review FROM {$CONF['sql_prefix']}_reviews WHERE username = '{$username}' AND active = 1";
	  if (isset($FORM['all_reviews'])) {
        $result = $DB->query("{$query} ORDER BY date DESC", __FILE__, __LINE__);
      }
      else {
        $result = $DB->select_limit("{$query} ORDER BY RAND()", 2, 0, __FILE__, __LINE__);
		$TMPL['reviews'] = "<center>Комментарии<br><a href='http://mctop.su/rating/server/{$id}/all_comments'>Показать</a> все мнения<br></center> ";
      }
      $TMPL['reviews'] .= '';
      while (list($TMPL['id'], $TMPL['date'], $TMPL['review']) = $DB->fetch_array($result)) {
        $TMPL['reviews'] .= $this->do_skin('stats_review');
		
	
      }

// Як index.php
// админ иль нет


      $TMPL['content'] = $this->do_skin('stats');
      $TMPL['content'] .= $this->show_ads();

	  
	  //
    
    }

    else {
      $this->error($LNG['g_invalid_u']);
    }
   //}
   //else {
 //  $TMPL['content'] = $this->do_skin('stats_inactive');
   //}
   } else {
			$TMPL['content'] = $this->do_skin('stats_dne');
   }
}
  }
}


?>
