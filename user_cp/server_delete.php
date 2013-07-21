<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class server_delete  extends base{
  function server_delete() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $TMPL['id'] = $DB->escape($FORM['id']);
    $TMPL['mode'] = $DB->escape($FORM['mode']);
    $TMPL['header'] = 'Удаление сервера';

	
    list($check) = $DB->fetch("SELECT 1 FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
    if ($check) {
      if (!isset($FORM['submit'])) {
        $this->form();
      }
      else {
	  
        $this->process();
      }
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
   $username = $TMPL['username'];
   $id = $TMPL['id'];

   list($check) = $DB->fetch("SELECT uid FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$TMPL['id']}' and username ='{$username}'", __FILE__, __LINE__);
   if(!empty($check))
   {
	$row = $DB->fetch("SELECT * FROM rtt_servers_real WHERE id = '{$TMPL['id']}' and username ='{$username}'", __FILE__, __LINE__);
	$mode = $TMPL['mode']; 
	if($mode == 1) {
		$LNG['server_delete_message_confirm'] = "<div class='message message_notice'><p><span>Подтверждение</span><br/> Вы хотите удалить сервер, привязанный к Вашему аккаунту, имеющий <br/> Название: {$row['title']}<br/> ID: {$row['id']}<br/>IP: {$row['ip']}<br/>Port: {$row['port']}<br/><br/>Если Вы действительно хотите удалить сервер, перейдите по ссылке: <a href='http://mctop.su/cp/server/delete/{$row['id']}/2'>http://mctop.su/cp/server/delete/{$row['id']}/2</a><br/></p></div>";
		$TMPL['user_cp_content'] = $LNG['server_delete_message_confirm'];
	} elseif($mode==2) {
		$query = "DELETE FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$id}' and username = '{$username}' ";
		$DB->query($query, __FILE__, __LINE__);
		$LNG['server_delete_message_success'] = "<div class='message message_success'><p><span>Уведомление</span><br/> Сервер был успешно удален</p></div>";
		$TMPL['user_cp_content'] = $LNG['server_delete_message_success'];
	}
}
else
{
$TMPL['error_text'] = 'Сервер не является Вашим. Ай яй яй';
$TMPL['user_cp_content'] = $this->do_skin('adv_error');
}
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	
    $TMPL['title'] = $DB->escape($FORM['title']);
    $TMPL['ip'] = $DB->escape($FORM['ip']);
    $TMPL['port'] = $DB->escape($FORM['port']);
    $TMPL['description'] = $DB->escape($FORM['description']);
    $TMPL['version'] = $DB->escape($FORM['version']);
	$TMPL['whitelist'] = intval($FORM['whitelist']);
	$TMPL['clienttype'] = intval($FORM['clienttype']);
	
  //echo $TMPL['active'];
    if (1==1) {
     

      $DB->query("UPDATE {$CONF['sql_prefix']}_servers_real SET title = '{$TMPL['title']}', clienttype = '{$TMPL['clienttype']}', version = '{$TMPL['version']}', whitelist = '{$TMPL['whitelist']}', description = '{$TMPL['description']}', ip = '{$TMPL['ip']}', port = '{$TMPL['port']}' WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
 
      $TMPL['user_cp_content'] = "<br>{$LNG['server_edit_info_success']}";
    }
    else {
      $this->form();
    }
  }
}
?>
