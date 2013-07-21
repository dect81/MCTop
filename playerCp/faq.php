<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class faq extends join_edit {
  function faq() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['user_cp_header'];
	$TMPL['user_cp_content'] = 'Раздел временно недоступен';
//$this->form();
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
#Делаем динамическое приветствие для юзера

$timestamp = time();
$time_st = strftime('%H',$timestamp);


if($TMPL['active']== 3)
{
$TMPL['user_cp_content'] = $this->do_skin('user_cp_banned');
}
else {
list($advertiser, $id) = $DB->fetch("SELECT advertiser, id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
if(!$advertiser) $TMPL['user_cp_content'] = $this->do_skin('user_cp_faq');
else $TMPL['user_cp_content'] = $this->do_skin('adv_cp_faq');
 }
}


}

       

?>