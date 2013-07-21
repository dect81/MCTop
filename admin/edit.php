<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class edit extends join_edit {
  function edit() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	//$TMPL['admin'] = 1;
    $TMPL['header'] = $LNG['a_edit_header'];

    $TMPL['error_username'] = '';
    $TMPL['error_style_username'] = '';
    $TMPL['error_password'] = '';
    $TMPL['error_style_password'] = '';
    $TMPL['error_url'] = '';
    $TMPL['error_style_url'] = '';
    $TMPL['error_email'] = '';
    $TMPL['error_style_email'] = '';
    $TMPL['error_title'] = '';
    $TMPL['error_style_title'] = '';
    $TMPL['error_captcha'] = '';
    $TMPL['error_style_captcha'] = '';

    $TMPL['username'] = $DB->escape($FORM['u']);
    list($check) = $DB->fetch("SELECT 1 FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
    if ($check) {
      if (!isset($FORM['submit'])) {
        $this->form();
      }
      else {
        $this->process();
      }
    }
    else {
      $this->error($LNG['g_invalid_u'], 'admin');
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;

    if (!isset($TMPL['url'])) {
      $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      $row_2 = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers_real WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row, $row_2);
    }
    else {
	  if (isset($TMPL['id'])) { $TMPL['id'] = stripslashes($TMPL['id']); }
      if (isset($TMPL['url'])) { $TMPL['url'] = stripslashes($TMPL['url']); }
      if (isset($TMPL['title'])) { $TMPL['title'] = stripslashes($TMPL['title']); }
      if (isset($TMPL['description'])) { $TMPL['description'] = stripslashes($TMPL['description']); }
	  if (isset($TMPL['det_description'])) { $TMPL['det_description'] = stripslashes($TMPL['det_description']); }
      if (isset($TMPL['serv_ip'])) { $TMPL['serv_ip'] = stripslashes($TMPL['serv_ip']); }
	  if (isset($TMPL['serv_port'])) { $TMPL['serv_port'] = stripslashes($TMPL['serv_port']); }
	  if (isset($TMPL['clienttype'])) { $TMPL['clienttype'] = stripslashes($TMPL['clienttype']); }
      if (isset($TMPL['category'])) { $TMPL['category'] = stripslashes($TMPL['category']); }
      if (isset($TMPL['email'])) { $TMPL['email'] = stripslashes($TMPL['email']); }
    }

    $TMPL['categories_menu'] = "<select name=\"category\">\n";
    foreach ($CONF['categories'] as $cat => $skin) {
      if ($TMPL['category'] == $cat) {
        $TMPL['categories_menu'] .= "<option value=\"{$cat}\" selected=\"selected\">{$cat}</option>\n";
      }
      else {
        $TMPL['categories_menu'] .= "<option value=\"{$cat}\">{$cat}</option>\n";
      }
    }
    $TMPL['categories_menu'] .= '</select>';

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

    $TMPL['url'] = htmlspecialchars($TMPL['url']);
    $TMPL['id'] = htmlspecialchars($TMPL['id']);
    $TMPL['title'] = htmlspecialchars($TMPL['title']);
    $TMPL['description'] = htmlspecialchars($TMPL['description']);
    $TMPL['email'] = htmlspecialchars($TMPL['email']);

    $TMPL['admin_content'] = <<<EndHTML

<form action="{$TMPL['list_url']}/admin/u/edit/{$TMPL['username']}" method="post">
<fieldset>
Статус сервера: <img src="{$list_url}/status/s{$TMPL['id']}/1" border="0" alt=""/><br>
Статус аккаунта: {$TMPL['active']}<br>
Перейти: в <a href="{$TMPL['list_url']}/rating/server/{$TMPL['id']}">  статистику</a> сервера

<br>
<br>


<legend>{$LNG['join_website']}</legend>
<div class="{$TMPL['error_style_url']}"><label>{$LNG['g_url']}<br />
<input type="text" name="url" size="50" value="{$TMPL['url']}" />
{$TMPL['error_url']}
</label></div><br />
<input type="text" name="banner_url" size="50" value="{$TMPL['banner_url']}" />
<div class="{$TMPL['error_style_title']}"><label>{$LNG['g_title']}<br />
<input type="text" name="title" size="50" value="{$TMPL['title']}" />
{$TMPL['error_title']}
</label></div><br />
<label>{$LNG['adm_edit_descr']}<br />
<textarea cols="80" rows="4" name="description">{$TMPL['description']}</textarea><br />
</label>
<br />
<label>{$LNG['adm_edit_clienttype']}<br />
<textarea cols="20" rows="1" name="clienttype">{$TMPL['clienttype']}</textarea><br />
</label>
<label>Версия сервера<br />
<textarea cols="20" rows="1" name="serv_version">{$TMPL['version']}</textarea><br />
</label>
<div class="{$TMPL['error_style_email']}"><label>{$LNG['g_email']}<br />
<input type="text" name="email" size="50" value="{$TMPL['email']}" />
{$TMPL['error_email']}
</label></div>
</fieldset>
<fieldset>
<legend>Инфо для проверки статуса</legend>
<div class="{$TMPL['error_style_title']}"><label>{$LNG['s_ipport']}<br />
<input type="text" name="serv_ip" size="25" value="{$TMPL['serv_ip']}" /> <input type="text" name="serv_port" size="7" value="{$TMPL['serv_port']}" />
{$TMPL['join_error_serv_ip']}
</label></div><br />
</fieldset>

<fieldset>

<label>{$LNG['a_edit_site_is']}<br />
{$TMPL['active_menu']}<br /><br />
</label>
<input name="submit" type="submit" value="{$LNG['a_edit_header']}" />
</fieldset>
</form>
	
	
EndHTML;
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['url'] = $DB->escape($FORM['url']);
    $TMPL['title'] = $DB->escape($FORM['title']);
    $TMPL['banner_url'] = $DB->escape($FORM['banner_url']);
    $TMPL['description'] = $DB->escape($FORM['description']);
	$TMPL['det_description'] = $DB->escape($FORM['det_description']);
	$TMPL['clienttype'] = $DB->escape($FORM['clienttype']);
    $TMPL['category'] = $DB->escape($FORM['category']);
    $TMPL['email'] = $DB->escape($FORM['email']);
	$TMPL['serv_ip'] = $DB->escape($FORM['serv_ip']);
    $TMPL['serv_port'] = $DB->escape($FORM['serv_port']);
    $TMPL['active'] = intval($FORM['active']);

    if ($this->check_input('edit')) {
      if ($FORM['password']) {
        $password = md5($FORM['password']);
        $password_sql = ", password = '{$password}'";
      }
      else {
        $password_sql = '';
      }

      require_once("{$CONF['path']}/sources/in.php");
      $short_url = in::short_url($TMPL['url']);

      $DB->query("UPDATE {$CONF['sql_prefix']}_servers SET url = '{$TMPL['url']}', banner_url = '{$TMPL['banner_url']}', short_url = '{$short_url}', serv_port = '{$TMPL['serv_port']}', serv_ip = '{$TMPL['serv_ip']}', title = '{$TMPL['title']}', description = '{$TMPL['description']}', det_description = '{$TMPL['det_description']}', clienttype = '{$TMPL['clienttype']}', category = '{$TMPL['category']}', email = '{$TMPL['email']}', active = {$TMPL['active']}{$password_sql} WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);

	  $TMPL['admin_content'] = $LNG['a_edit_edited'];
    }
    else {
      $this->form();
    }
  }
}
?>
