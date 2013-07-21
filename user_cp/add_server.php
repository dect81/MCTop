<?php
//==============================\\


if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}
$TMPL['page_name_title'] = '<a href="http://mctop.ru/cp/main">Контрольная панель</a>';
class add_server extends join_edit {
  function add_server() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = "Добавление нового сервера в проект";

    $TMPL['error_title'] = '';
    $TMPL['error_version'] = '';
    $TMPL['error_ip'] = '';
    $TMPL['error_port'] = '';
    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $FORM, $LNG, $TMPL;

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
      $TMPL['clienttype_menu'] .= "<option value=\"0\" selected=\"selected\">Пиратка</option>\n<option value=\"1\">Лицензия</option>\n";
    }
    if ($TMPL['clienttype'] == 1) {
      $TMPL['clienttype_menu'] .= "<option value=\"1\" selected=\"selected\">Лицензия</option>\n\n<option value=\"0\">Пиратка</option>\n";
    }
    $TMPL['clienttype_menu'] .= '</select>';

	$TMPL['server_type_menu'] = "<select name=\"server_type\">\n";
      $TMPL['server_type_menu'] .= "
	  <option value=\"1\" selected=\"selected\">Industrial</option>\n
	  <option value=\"2\">Creative</option>\n
	  <option value=\"3\">Survival</option>\n
	  ";
    $TMPL['server_type_menu'] .= '</select>';
	
    if (!isset($TMPL['description'])) { $TMPL['description'] = ''; }
    if (!isset($TMPL['serv_version'])) { $TMPL['serv_version'] = ''; }
    if (!isset($TMPL['serv_ip'])) { $TMPL['serv_ip'] = ''; }
    if (!isset($TMPL['port'])) { $TMPL['port'] = '25565'; }


    if (isset($TMPL['title'])) { $TMPL['title'] = stripslashes($TMPL['title']); }
    if (isset($TMPL['description'])) { $TMPL['description'] = stripslashes($TMPL['description']); }
    if (isset($TMPL['serv_version'])) { $TMPL['serv_version'] = stripslashes($TMPL['serv_version']); }
    if (isset($TMPL['serv_ip'])) { $TMPL['serv_ip'] = stripslashes($TMPL['serv_ip']); }
    if (isset($TMPL['port'])) { $TMPL['port'] = (int)$TMPL['port']; }
    
    $TMPL['user_cp_content'] = $this->do_skin('cp/server_add');
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $FORM['description'] = str_replace(array("\r\n", "\n", "\r"), '<br/>', $FORM['description']);
    $TMPL['description'] = $DB->escape($FORM['description'], 1);
	echo $TMPL['title'];
    $TMPL['title'] = $DB->escape($FORM['title'], 1);

    $TMPL['clienttype'] = $DB->escape($FORM['clienttype'], 1);
    $TMPL['serv_version'] = $DB->escape($FORM['serv_version'], 1);
    $TMPL['serv_ip'] = $DB->escape($FORM['serv_ip'], 1);
    $TMPL['port'] = $DB->escape($FORM['port'], 1);
    
    $TMPL['title'] = $this->bad_words($TMPL['title']);

	$TMPL['whitelist'] = intval($FORM['whitelist']);
	$TMPL['clienttype'] = intval($FORM['clienttype']);
	$TMPL['server_type'] = intval($FORM['server_type']);
	$username = $TMPL['username'];
    if ($this->check_ban('join')) {
      if ($this->add_server_check_input()) {
	
 		list($uid) = $DB->fetch("SELECT id FROM {$CONF['sql_prefix']}_servers where username = '{$username}'", __FILE__, __LINE__);  	
 		list($id) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_servers_real", __FILE__, __LINE__);  	
       $DB->query("INSERT INTO {$CONF['sql_prefix']}_servers_real (id, uid, ip, port, title, description, version, whitelist, clienttype, username, active, main_serv, status, server_type)
                  VALUES ({$id}, '{$uid}', '{$TMPL['serv_ip']}', '{$TMPL['port']}', '{$TMPL['title']}', '{$TMPL['description']}', '{$TMPL['serv_version']}', '{$TMPL['whitelist']}','{$TMPL['clienttype']}','{$TMPL['username']}','1','0','2','{$TMPL['server_type']}')", __FILE__, __LINE__);

		  $TMPL['user_cp_content'] = '<br/><hr/><h3>Заявка на добавление сервера принята.</h3><hr size ="1"/>';
          $TMPL['user_cp_content'] .= $LNG['add_server_approve'];
      }
      else {
        $this->form();
      }
    }
    else {
      $this->form();
    }
  }
}
?>
