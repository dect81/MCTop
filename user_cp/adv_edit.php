<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class adv_edit extends join_edit {
  function adv_edit() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $TMPL['id'] = $DB->escape($FORM['id']);
   
    $TMPL['header'] = $LNG['a_edit_header'];
    $TMPL['error_title'] = '';
    $TMPL['error_url'] = '';
    $TMPL['error_descr'] = '';
    $TMPL['error_banner_url'] = '';

	
    list($check) = $DB->fetch("SELECT 1 FROM {$CONF['sql_prefix']}_adv WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
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
   list($uid) = $DB->fetch("SELECT id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);

   list($check) = $DB->fetch("SELECT uid FROM {$CONF['sql_prefix']}_adv WHERE id = '{$TMPL['id']}' and uid ='{$uid}'", __FILE__, __LINE__);
   if(!empty($check))
   {
    if (!isset($TMPL['url'])) {
      $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_adv WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row);
    }
    else {
	  if (isset($TMPL['id'])) { $TMPL['id'] = stripslashes($TMPL['id']); }
      if (isset($TMPL['title'])) { $TMPL['title'] = stripslashes($TMPL['title']); }
      if (isset($TMPL['url'])) { $TMPL['url'] = stripslashes($TMPL['url']); }
      if (isset($TMPL['site'])) { $TMPL['site'] = stripslashes($TMPL['site']); }
      if (isset($TMPL['img_url'])) { $TMPL['img_url'] = stripslashes($TMPL['img_url']); }
      if (isset($TMPL['descr'])) { $TMPL['descr'] = stripslashes($TMPL['descr']); }
    }

    $TMPL['active_menu'] = "<select name=\"active\">\n";
    if ($TMPL['active'] == 1) {
      $TMPL['active_menu'] .= "<option value=\"1\" selected=\"selected\">Активно</option>\n<option value=\"0\">Неактивно</option>";
    }
    if ($TMPL['active'] == 0) {
      $TMPL['active_menu'] .= "<option value=\"0\" selected=\"selected\">Неактивно</option>\n<option value=\"1\">Активно</option>";
    }
    $TMPL['active_menu'] .= '</select>';

    if ($CONF['max_banner_width'] && $CONF['max_banner_height']) {
      $TMPL['join_banner_size'] = sprintf($LNG['join_banner_size'], $CONF['max_banner_width'], $CONF['max_banner_height']);
    }
    else {
      $TMPL['join_banner_size'] = '';
    }

    $TMPL['id'] = htmlspecialchars($TMPL['id']);
    $TMPL['title'] = htmlspecialchars($TMPL['title']);
    $TMPL['url'] = htmlspecialchars($TMPL['url']);
    $TMPL['site'] = htmlspecialchars($TMPL['site']);
    $TMPL['img_url'] = htmlspecialchars($TMPL['img_url']);
    $TMPL['descr'] = htmlspecialchars($TMPL['descr']);

    $TMPL['user_cp_content'] = <<<EndHTML
{$TMPL['error_url']}
{$TMPL['error_title']}
{$TMPL['error_descr']}
{$TMPL['error_banner_url']}
<form action="{$TMPL['list_url']}/cp/adv/edit/{$TMPL['id']}" method="post">
<fieldset>
<legend>Настройки объявления</legend>
<label><a title='Адрес, на который перейдет пользователь по нажатию на объявление'>Целевой URL</a><br />
<input type="text" name="url" size="50" value="{$TMPL['url']}" />

</label><br /><br />

<label>Заголовок<br />
<input type="text" name="title" size="50" value="{$TMPL['title']}" />

</label><br /><br />

<label>Описание<br />
<textarea cols="80" rows="4" name="descr">{$TMPL['descr']}</textarea>

<br /><br />
</label>

<label>{$LNG['g_banner_url']} (Максимальный размер: 135x90)<br />
<input type="text" name="img_url" size="90" value="{$TMPL['img_url']}" />

</label><br /><br />

<label>Это объявление: {$TMPL['active_menu']}
</label><br/ ><br/ >
<input name="submit" type="submit" value="Редактировать объявление" />
</fieldset>
</form>
	
	
EndHTML;
}
else
{
$TMPL['error_text'] = 'Объявление не является Вашим. Ай яй яй';
$TMPL['user_cp_content'] = $this->do_skin('adv_error');
}
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	
    $TMPL['title'] = $DB->escape($FORM['title']);
    $TMPL['url'] = $DB->escape($FORM['url']);
    $TMPL['site'] = $DB->escape($FORM['site']);
    $TMPL['img_url'] = $DB->escape($FORM['img_url']);
    $TMPL['descr'] = $DB->escape($FORM['descr']);
    $TMPL['active'] = intval($FORM['active']);
  //echo $TMPL['active'];
    if ($this->adv_check_input('edit')) {
     

      $DB->query("UPDATE {$CONF['sql_prefix']}_adv SET title = '{$TMPL['title']}', url = '{$TMPL['url']}', site = '{$site}', descr = '{$TMPL['descr']}', img_url = '{$TMPL['img_url']}', active = {$TMPL['active']} WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
 
      $TMPL['user_cp_content'] = "<br>{$LNG['adv_success_changed']}";
    }
    else {
      $this->form();
    }
  }
}
?>
