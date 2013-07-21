<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class server_edit  extends base{
  function server_edit() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $TMPL['id'] = $DB->escape($FORM['id']);
   
    $TMPL['header'] = 'Редактирование сервера';

	
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
    if (!isset($TMPL['url'])) {
      $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row);
    }
    else {
	  if (isset($TMPL['id'])) { $TMPL['id'] = stripslashes($TMPL['id']); }
      if (isset($TMPL['title'])) { $TMPL['title'] = stripslashes($TMPL['title']); }
      if (isset($TMPL['description'])) { $TMPL['description'] = stripslashes($TMPL['description']); }
      if (isset($TMPL['version'])) { $TMPL['version'] = stripslashes($TMPL['version']); }
      if (isset($TMPL['whitelist'])) { $TMPL['whitelist'] = stripslashes($TMPL['whitelist']); }
      if (isset($TMPL['clienttype'])) { $TMPL['clienttype'] = stripslashes($TMPL['clienttype']); }
      if (isset($TMPL['port'])) { $TMPL['description'] = stripslashes($TMPL['port']); }
      if (isset($TMPL['ip'])) { $TMPL['description'] = stripslashes($TMPL['ip']); }

    }
    $TMPL['whitelist_menu'] = "<select name=\"whitelist\">\n";
    if ($TMPL['whitelist'] == 0) {
      $TMPL['whitelist_menu'] .= "<option value=\"0\" selected=\"selected\">Выключен</option>\n<option value=\"1\">Включен</option>\n";
    }
    if ($TMPL['whitelist'] == 1) {
      $TMPL['whitelist_menu'] .= "<option value=\"1\" selected=\"selected\">Включен</option>\n\n<option value=\"0\">Выключен</option>\n";
    }
    $TMPL['whitelist_menu'] .= '</select>';
	
	$TMPL['clienttype_menu'] = "<select name=\"clienttype\">\n";
    if ($TMPL['clienttype'] == 0) {
      $TMPL['clienttype_menu'] .= "<option value=\"0\" selected=\"selected\">Пиратская копия</option>\n<option value=\"1\">Лицензия</option>\n";
    }
    if ($TMPL['clienttype'] == 1) {
      $TMPL['clienttype_menu'] .= "<option value=\"1\" selected=\"selected\">Лицензия</option>\n\n<option value=\"0\">Пиратская копия</option>\n";
    }
    $TMPL['clienttype_menu'] .= '</select>';
	
	$TMPL['server_type_menu'] = "<select name=\"server_type\">\n";
    if ($TMPL['server_type'] == 0) {
      $TMPL['server_type_menu'] .= "
	  <option value=\"0\" selected=\"selected\">Не установлен</option>\n
	  <option value=\"1\">Industrial</option>\n
	  <option value=\"2\">Creative</option>\n
	  <option value=\"3\">Survival</option>\n
	  ";
    }
    elseif ($TMPL['server_type'] == 1) {
      $TMPL['server_type_menu'] .= "
	  <option value=\"1\" selected=\"selected\">Industrial</option>\n
	  <option value=\"2\">Creative</option>\n
	  <option value=\"3\">Survival</option>\n
	  ";
    }
	elseif ($TMPL['server_type'] == 2) {
      $TMPL['server_type_menu'] .= "
	  <option value=\"1\">Industrial</option>\n
	  <option value=\"2\" selected=\"selected\">Creative</option>\n
	  <option value=\"3\">Survival</option>\n
	  ";
    }
	elseif ($TMPL['server_type'] == 3) {
      $TMPL['server_type_menu'] .= "
	  <option value=\"1\">Industrial</option>\n
	  <option value=\"2\">Creative</option>\n
	  <option value=\"3\" selected=\"selected\">Survival</option>\n
	  ";
    }
    $TMPL['server_type_menu'] .= '</select>';
	
	if($TMPL['query_port']<>-1)
		$TMPL['query_check']='checked';
	
    if ($CONF['max_banner_width'] && $CONF['max_banner_height']) {
      $TMPL['join_banner_size'] = sprintf($LNG['join_banner_size'], $CONF['max_banner_width'], $CONF['max_banner_height']);
    }
    else {
      $TMPL['join_banner_size'] = '';
    }

    $TMPL['id'] = htmlspecialchars($TMPL['id']);
    $TMPL['title'] = htmlspecialchars($TMPL['title']);
    $TMPL['url'] = htmlspecialchars($TMPL['url']);
    $TMPL['ip'] = htmlspecialchars($TMPL['ip']);
    $TMPL['version'] = htmlspecialchars($TMPL['version']);
    $TMPL['clienttype'] = htmlspecialchars($TMPL['clienttype']);
    $TMPL['whitelist'] = htmlspecialchars($TMPL['whitelist']);

	$TMPL['user_cp_content'] = $this->do_skin('cp/server_edit');
	
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
	$TMPL['server_type'] = intval($FORM['server_type']);
	
	if(intval($FORM['queryType'])){
	
		$TMPL['query_port'] = intval($FORM['queryPort']);	
		
		$socket = @fsockopen($TMPL['ip'], $TMPL['query_port'], $errno, $errstr, 0.6);
				
		if ($socket) {									
					
			$response = null;
			fwrite($socket, "MCTQuery\n");
			
			while (!feof($socket)) {
				$response .= fgets($socket, 512);
			}
			fclose($socket);
			$response = json_decode($response);
			$DB->query("UPDATE {$CONF['sql_prefix']}_servers SET mctq_time = 1 WHERE id = '{$TMPL['uid']}'", __FILE__, __LINE__);
					
		}
		else {
			$TMPL['query_port'] = -1;
			$TMPL['user_cp_content'] = "<br>{$LNG['server_edit_info_unsuccess']}";
			$DB->query("UPDATE {$CONF['sql_prefix']}_servers SET mctq_time = -1 WHERE id = '{$TMPL['uid']}'", __FILE__, __LINE__);
			$rendered = 1;
		}
		
	}
	else 
		$TMPL['query_port'] = -1;
		
	$DB->query("UPDATE {$CONF['sql_prefix']}_servers_real SET title = '{$TMPL['title']}', clienttype = '{$TMPL['clienttype']}', version = '{$TMPL['version']}', whitelist = '{$TMPL['whitelist']}', description = '{$TMPL['description']}', ip = '{$TMPL['ip']}', port = '{$TMPL['port']}', server_type = '{$TMPL['server_type']}', query_port = '{$TMPL['query_port']}' WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);

	if(!$rendered)
		$TMPL['user_cp_content'] = "<br>{$LNG['server_edit_info_success']}";

  }
}
?>
