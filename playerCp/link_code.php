<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class link_code extends join_edit {
  function link_code() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['user_cp_header'];

      $this->form();

  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
list($advertiser) = $DB->fetch("SELECT advertiser FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);

    if (!$advertiser){
      $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row);


    }



if(!$advertiser) $TMPL['user_cp_content'] = $this->do_skin('link_code');
else {
$TMPL['error_text'] = 'Данная функция доступна только для администраторов серверов.';
$TMPL['user_cp_content'] = $this->do_skin('adv_error');
}
}


  }

 

?>
