<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class settings extends join_edit {
  function settings() {
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
	
$row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_user_settings WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
$TMPL = array_merge($TMPL, $row);
$m_s_data = $TMPL['m_s_data'];
$m_ot_data = $TMPL['m_ot_data'];

//Данные о сервере
    $TMPL['m_s_data'] = "<select name=\"m_s_data\">\n";
    if ($m_s_data == 1) {
	  $TMPL['m_s_data'] .= "<option value=\"1\">Без изменений</option>\n";
      $TMPL['m_s_data'] .= "<option value=\"0\">Выключить</option>\n";
    }
    if ($m_s_data == 0) {
	  $TMPL['m_s_data'] .= "<option value=\"0\">Без изменений</option>\n";
      $TMPL['m_s_data'] .= "<option value=\"1\">Включить</option>\n";

    }
    $TMPL['m_s_data'] .= '</select>';
//Данные об аккаунте
    $TMPL['m_ot_data'] = "<select name=\"m_ot_data\">\n";
    if ($m_ot_data == 1) {
	  $TMPL['m_ot_data'] .= "<option value=\"1\">Без изменений</option>\n";
      $TMPL['m_ot_data'] .= "<option value=\"0\">Выключить</option>\n";
    }
    if ($m_ot_data == 0) {
	  $TMPL['m_ot_data'] .= "<option value=\"0\">Без изменений</option>\n";
      $TMPL['m_ot_data'] .= "<option value=\"1\">Включить</option>\n";
    }
    $TMPL['m_ot_data'] .= '</select>';	
	


    $TMPL['user_cp_content'] = $this->do_skin('user_cp_settings');
  }

 function process()
 {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $m_s_data = intval($FORM['m_s_data']);
    $m_ot_data = intval($FORM['m_ot_data']);
    $DB->query("UPDATE {$CONF['sql_prefix']}_user_settings SET m_ot_data = $m_ot_data, m_s_data = $m_s_data WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
    $TMPL['user_cp_content'] = $this->do_skin('edit_finish');	

 }
}
?>