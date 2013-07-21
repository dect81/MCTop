<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class servers extends join_edit {
  function servers() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['user_cp_header'];

    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
	$username = $TMPL['username'];
	$uid = mysql_fetch_row(mysql_query("SELECT id from rtt_servers where username = '{$username}'"));
	$uid = $uid[0];
	$TMPL['user_cp_content'] = "<h3><a href='{$list_url}/cp/server/new'>Добавить сервер в проект</a></h3><hr size ='1'/>";

	$result = $DB->query("SELECT * FROM {$CONF['sql_prefix']}_servers_real WHERE uid = {$uid} order by id desc", __FILE__, __LINE__);
	while ($row = $DB->fetch_array($result)) 
	{

		$server = array_merge($TMPL, $row);

		if ($server['active'] == 1) 
			$active = 'Активен';
		elseif($server['active'] == 2) 
			$active = 'Не активен';
		elseif($server['active'] == 0) 
			$active = 'На проверке';
		
		$form = <<<HTML
		<a href="{$list_url}/cp/server/edit/{$server['id']}"><table border='0' cellspacing = '3'>
		<tr><td>ID сервера:</td><td>{$server['id']}</td></tr>
		<tr><td>Активность сервера:</td><td>$active</td></tr>
		<tr><td>IP:</td><td>{$server['ip']}</td></tr>
		<tr><td>Порт:</td><td>{$server['port']}</td></tr>
		<tr><td><a href="http://mctop.su/cp/server/delete/{$server['id']}/1">Удалить сервер</a></td></tr>
		</table></a>
		<hr size ="1">
HTML;
		$TMPL['user_cp_content'] .= $form;
	}

  }

       
}

?>