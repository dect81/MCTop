<?php
//==============================\\
//             L.R.E            \\
//     Created by RaM Team      \\
//  Based on Aardvark Topsite   \\
//==============================\\ 

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class manage_ap extends base {
  function manage_ap() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['adm_manage_ap_header'];

    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $DB, $LNG, $CONF, $TMPL;

//$admin = $ADM['admin'];
list($adm_msg) = $DB->fetch("SELECT adm_msg FROM {$CONF['sql_prefix']}_admin", __FILE__, __LINE__);
list($user_msg) = $DB->fetch("SELECT user_msg FROM {$CONF['sql_prefix']}_admin", __FILE__, __LINE__);

$admin = $_COOKIE['lrengine_un'];

$TMPL['admin_content'] = <<<EndHTML
<form action="{$list_url}/admin/o/message" method="post">
<table align="center" border="1" cellpadding="0" cellspacing="0">

<hr size="1">
<legend>{$LNG['adm_manage_ap_message']}<hr size="1"></legend>
<label>{$LNG['adm_manage_ap_message_by']}<br />
<input type="text" name="admin" size="50" value="$admin" /><br /><br />
</label>
<label>{$LNG['adm_manage_ap_message_text']}<br />
<textarea cols="75" rows="4" name="adm_msg">$adm_msg</textarea><br />
</label><br><hr size="1">
<label>Внимание! Обращаться аккуратно ! Сообщение для хостеров!<br /><hr size="1"><br>
<textarea cols="75" rows="4" name="user_msg">$user_msg</textarea><br />
</label>
<tr><input name="submit" type="submit" value="{$LNG['a_s_header']}" /></tr>
</table>

</form>
EndHTML;
  }

  function process() {
    global $DB, $FORM, $CONF, $LNG, $TMPL;

    $admin = $DB->escape($FORM['admin']);
    $adm_msg = $DB->escape($FORM['adm_msg']);
    $user_msg = $DB->escape($FORM['user_msg']);

    $DB->query("UPDATE {$CONF['sql_prefix']}_admin SET adm_msg = '{$adm_msg}', admin = '{$admin}', user_msg = '{$user_msg}'", __FILE__, __LINE__);
    $TMPL['admin_content'] = $LNG['a_s_updated'];
  }
}
?>
