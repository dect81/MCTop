<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class vote extends join_edit {
  function vote() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $TMPL['id'] = $DB->escape($FORM['id']);
   
    $TMPL['header'] = $LNG['a_edit_header'];
    $TMPL['error_title'] = '';
    $TMPL['error_url'] = '';
    $TMPL['error_descr'] = '';
    $TMPL['error_banner_url'] = '';

	
    list($check) = $DB->fetch("SELECT 1 FROM {$CONF['sql_prefix']}_votes WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
    if ($check) {
      if (!isset($FORM['submit'])) {
        $this->form();
      }
      else {
	  
        $this->process();
      }
    }
  }
 
/**
 * Convert number of seconds into hours, minutes and seconds
 * and return an array containing those values
 *
 * @param integer $seconds Number of seconds to parse
 * @return array
 */
function sec_to_his ($seconds)
{
  $seconds = $seconds -3600;
  return gmdate ('H:i:s', $seconds);
}
  function form() {
    global $CONF, $DB, $LNG, $TMPL;
	$uid = $_COOKIE['uid'];
   list($check) = $DB->fetch("SELECT vk_id FROM {$CONF['sql_prefix']}_votes WHERE id = '{$TMPL['id']}' and vk_id ='{$uid}'", __FILE__, __LINE__);
   if(!empty($check))
   {
    if (!isset($TMPL['url'])) {
      $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_votes WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row);
    }
    else {
	  if (isset($TMPL['id'])) { $TMPL['id'] = stripslashes($TMPL['id']); }
      if (isset($TMPL['username'])) { $TMPL['username'] = stripslashes($TMPL['username']); }
      if (isset($TMPL['url'])) { $TMPL['url'] = stripslashes($TMPL['url']); }
      if (isset($TMPL['site'])) { $TMPL['site'] = stripslashes($TMPL['site']); }
      if (isset($TMPL['img_url'])) { $TMPL['img_url'] = stripslashes($TMPL['img_url']); }
      if (isset($TMPL['descr'])) { $TMPL['descr'] = stripslashes($TMPL['descr']); }
    }

    /*$TMPL['debug_menu'] = "<select name=\"debug\">\n";
    if ($TMPL['debug'] == 0) {
      $TMPL['debug_menu'] .= "<option value=\"0\" selected=\"selected\">проблем нет</option>\n<option value=\"1\">обнаружены проблемы</option>";
    }
    if ($TMPL['debug'] == 1) {
      $TMPL['debug_menu'] .= "<option value=\"1\" selected=\"selected\">обнаружены проблемы</option>\n<option value=\"0\">проблем нет</option>";
    }
    $TMPL['debug_menu'] .= '</select>';

    if ($CONF['max_banner_width'] && $CONF['max_banner_height']) {
      $TMPL['join_banner_size'] = sprintf($LNG['join_banner_size'], $CONF['max_banner_width'], $CONF['max_banner_height']);
    }
    else {
      $TMPL['join_banner_size'] = '';
    }
*/
    $TMPL['id'] = htmlspecialchars($TMPL['id']);
    $TMPL['username'] = htmlspecialchars($TMPL['username']);
    $TMPL['url'] = htmlspecialchars($TMPL['url']);
    $TMPL['site'] = htmlspecialchars($TMPL['site']);
    $TMPL['img_url'] = htmlspecialchars($TMPL['img_url']);
    $TMPL['descr'] = htmlspecialchars($TMPL['descr']);
    $time = $TMPL['time'];
    $vote = $TMPL['vote'];
    $time +=3600;
    $tw = $time - (time()); 
	if ($tw<0) {
	$tw = "<b>Вы можете <a href='http://mctop.su/rating/vote/{$TMPL['username']}'>подтвердить</a> голос</b>";
	} else {
        $tw = $this->sec_to_his($tw);
	}
	if($vote == 1) {
	$tw = 'голос подтвержден';
	}
	$time = date('Y/m/d H:i', $time);
    $TMPL['user_cp_content'] = <<<EndHTML
{$TMPL['error_url']}
{$TMPL['error_title']}
{$TMPL['error_descr']}
{$TMPL['error_banner_url']}
<!--<form action="{$TMPL['list_url']}/playerCp/stats/vote/{$TMPL['id']}" method="post">-->
<fieldset>
<legend>Сведения о голосе</legend>
</br></br>
<label>Логин администратора сервера: <b>{$TMPL['username']}</b><br /></label><br /><br />
<label>Подтвердить голос можно после: <b>{$time}</b><br /></label><br /><br />
<label>Времени до потверждения: <b>{$tw}</b><br /></label><br /><br />
<label>debug: <b>{$TMPL['debug']}</b><br /></label><br /><br />
</label>


<!--<label>С этим голосом: {$TMPL['debug_menu']}
</label><br/ ><br/ >
<input name="submit" type="submit" value="Сохранить изменения" />
</fieldset>
</form>-->
	
	
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
	

    $TMPL['debug'] = intval($FORM['debug']);
     

      $DB->query("UPDATE {$CONF['sql_prefix']}_votes SET debug = {$TMPL['debug']} WHERE id = '{$TMPL['id']}'", __FILE__, __LINE__);
 
      $TMPL['user_cp_content'] = "<br>{$LNG['adv_success_changed']}";
      $TMPL['user_cp_content'] .= "<br><a href='http://mctop.su/playerCp/stats/vote/{$TMPL['id']}'>Вернуться назад</a> ";

  }
}
?>
