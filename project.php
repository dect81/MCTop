<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}


class project extends base {
  function project() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = '';

    $TMPL['id'] = $DB->escape($FORM['id'], 1);
	
	if($TMPL['id'] == 928) $TMPL['star'] = '<img src="http://mctop.su/images/star2.png"/>';
	
	$id = $TMPL['id'];

	list($username, $active) = $DB->fetch("SELECT username, active FROM {$CONF['sql_prefix']}_servers WHERE id = '{$id}'", __FILE__, __LINE__);
    list($check) = $DB->fetch("SELECT uid FROM {$CONF['sql_prefix']}_servers_real WHERE username = '{$username}'", __FILE__, __LINE__);
	
	if($active == 3) {
		$TMPL['content'] = "<br/></br><div class='message message_error'><p><span>Внимание!</span><br/> Ваш проект был исключен из рейтинга<br/>Возможно, это было сделано с добрыми намерениями, свяжитесь с <a href='http://vk.com/mctop'>администрацией</a>.</p></div>";	
	} 
	elseif($active == 4) {
		$TMPL['content'] = "<br/></br><div class='message message_notice'><p><span>Уведомление</span><br/> Ваш проект не участвует в рейтинге, до тех пор пока за него не будет отдан 1 голос.</p></div>";
	}				
	elseif ($active == 1) {
	if(!isset($check)){
		$TMPL['content'] = "<br/></br><div class='message message_error'><p><span>Уведомление</span><br/> Проекта с таким ID не существует</p></div>";
	} else {
    $server = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
    $stats = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers_real WHERE uid = '{$TMPL['id']}'", __FILE__, __LINE__);
    $stats_2 = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_stats WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);


	$TMPL = array_merge($TMPL,  $stats, $stats_2, $server);
	
	$TMPL['page_name_title'] = $TMPL['title'];

	$day = date('j');
    session_start();
      $TMPL['header'] .= "  {$TMPL['title']}";
	if(!isset($_SESSION["project_{$server['id']}"]) or (empty($_SESSION["project_{$server['id']}"]))){
		$_SESSION["project_{$server['id']}"] = "1";
		$query = "UPDATE `rtt_graphics_data` SET day_{$day}_views = day_{$day}_views +1  WHERE `username`='{$TMPL['username']}'";
		$DB->query($query, __FILE__, __LINE__);
		$query = "UPDATE `rtt_stats` SET views = views + 1  WHERE `username`='{$TMPL['username']}'";
		$DB->query($query, __FILE__, __LINE__);
	} 
		
	$username = $TMPL['username'];

	$res = mysql_query("SELECT id, title from rtt_servers_real where username='{$username}'");	
	while ($server = mysql_fetch_assoc($res)){	
		//$TMPL['form'] .= "<a href='http://mctop.su/rating/server/{$server['id']}'>{$server['title']}</a>";	
		$TMPL['form'] .= "<a href='{$TMPL['list_url']}/tooltip.php?&id={$server['id']}&link={$TMPL['list_url']}/rating/server/{$server['id']}' class='jTip' id='server_{$server['id']}' name='{$server['title']}'>{$server['title']}</a>";	
	}

	$TMPL['banner'] = "<a href= '{$TMPL['list_url']}/rating/server/out/{$TMPL['id']}'><img  src='{$TMPL['banner_url']}'/></a>";
	$TMPL['content'] = $this->do_skin('project');
}
}

  }

 
}




?>
