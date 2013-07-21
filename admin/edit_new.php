<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class edit_new  extends base{
  function edit_new() {
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


    if (!isset($TMPL['url'])) {
      $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers_real WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row);
    }
    else {
	  if (isset($TMPL['id'])) { $TMPL['id'] = stripslashes($TMPL['id']); }
      if (isset($TMPL['title'])) { $TMPL['title'] = stripslashes($TMPL['title']); }
      if (isset($TMPL['description'])) { $TMPL['description'] = stripslashes($TMPL['description']); }
      if (isset($TMPL['port'])) { $TMPL['description'] = stripslashes($TMPL['port']); }
      if (isset($TMPL['ip'])) { $TMPL['description'] = stripslashes($TMPL['ip']); }

    }


    $TMPL['id'] = htmlspecialchars($TMPL['id']);
    $TMPL['title'] = htmlspecialchars($TMPL['title']);
    $TMPL['url'] = htmlspecialchars($TMPL['url']);
    $TMPL['ip'] = htmlspecialchars($TMPL['ip']);
    $TMPL['port'] = htmlspecialchars($TMPL['port']);
    $TMPL['active_menu'] = "<select name=\"active\">\n";

    if ($TMPL['active'] == 1) {
      $TMPL['active_menu'] .= "<option value=\"1\" selected=\"selected\">Без изменений</option>\n<option value=\"2\">В неактивные</option>\n<option value=\"3\">Заблокировать!</option>";
    }
    if ($TMPL['active'] == 3) {
      $TMPL['active_menu'] .= "<option value=\"3\" selected=\"selected\">Без изменений</option>\n<option value=\"1\">Активировать</option>";
    }
    if ($TMPL['active'] == 0) {
      $TMPL['active_menu'] .= "<option>Активировать можно только со страницы ''подтверждение''</option>";
    }
    if ($TMPL['active'] == 2) {
      $TMPL['active_menu'] .= "<option value=\"2\" selected=\"selected\">Без изменений</option>\n<option value=\"1\">Активировать</option>\n<option value=\"3\">Заблокировать!</option>\n";
    }
    $TMPL['active_menu'] .= '</select>';

    $TMPL['admin_content'] = <<<EndHTML
<br/><br/><br/>
{$TMPL['error_url']}
{$TMPL['error_title']}
{$TMPL['error_descr']}
{$TMPL['error_banner_url']}
<form action="{$TMPL['list_url']}/cp/server/edit/{$TMPL['id']}" method="post">
<fieldset>
<legend>Настройки сервера</legend>


<label>Заголовок<br />
<input type="text" name="title" size="50" value="{$TMPL['title']}" />

</label><br /><br />
<div class="{$error_style_serv_ip}"><label>IP сервера <input type="text" name="ip" size="20" value="{$TMPL['ip']}" />{$error_serv_ip}</div></label><br/>


<div class="{$error_style_serv_port}"><label>Port сервера <input type="text" name="port" size="20" value="{$TMPL['port']}" />{$error_serv_ip}</div></label><br/>

<label>Описание<br />
<textarea cols="80" rows="4" name="description">{$TMPL['description']}</textarea>

<br /><br />
</label>
{$TMPL['active_menu']}
<a href="{$list_url}/admin/u/approve_new">Вернуться назад </a>
<input name="submit" type="submit" value="Редактировать объявление" />
</fieldset>
</form>
	
	
EndHTML;

  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	
    $TMPL['title'] = $DB->escape($FORM['title']);
    $TMPL['ip'] = $DB->escape($FORM['ip']);
    $TMPL['port'] = $DB->escape($FORM['port']);
    $TMPL['description'] = $DB->escape($FORM['description']);

  //echo $TMPL['active'];
    if (1==1) {
     

      $DB->query("UPDATE {$CONF['sql_prefix']}_servers_real SET title = '{$TMPL['title']}', description = '{$TMPL['description']}', ip = '{$TMPL['ip']}', port = '{$TMPL['port']}', active = {$TMPL['active']} WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
 
      $TMPL['admin_content'] = "<br>{$LNG['server_info_success_changed']}";
    }
    else {
      $this->form();
    }
  }
}
?>
