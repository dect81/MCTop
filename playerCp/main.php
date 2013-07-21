<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class main extends join_edit {
  function main() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['user_cp_header'];

      $this->form();

  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
/*	list($user_msg) = $DB->fetch("SELECT user_msg FROM {$CONF['sql_prefix']}_admin", __FILE__, __LINE__);
    $TMPL['user_msg'] = $user_msg;
    if ($TMPL['user_msg'] <> '') {
		$TMPL['user_msg'] = "<br/><br/><hr/><b>Администрация:</b> $user_msg";
    }	*/
/*foreach($TMPL as $key=>$num) 
echo "\$TMPL[".$key."] = ".$num."<br>";*/ 


$timestamp = time();
$time_st = strftime('%H',$timestamp);

#Делаем динамическое приветствие для юзера
if ($time_st <6 or $time_st >= 22 )
{
$TMPL['time_st'] = 'Доброй ночи';
}
elseif($time_st <= 22 and $time_st >= 17 )
{
$TMPL['time_st'] = 'Добрый вечер';
}
elseif($time_st <=17 and $time_st >= 12 )
{
$TMPL['time_st'] = 'Добрый день';
}
elseif($time_st <=12 and $time_st >= 6 )
{
$TMPL['time_st'] = 'Доброе утро';
}

//if($TMPL['active']== 3){$TMPL['user_cp_content'] = $this->do_skin('user_cp_banned');}




  if($TMPL['active']==1)$TMPL['active'] = "активен";
  else $TMPL['active'] = "неактивен; <a title='Смотрите вопрос 5 в FAQ' href='{$site_url}/cp/faq'> (Модераторы активируют Ваш аккаунт при следующей проверке)</a>";
$TMPL['user_cp_content'] = $this->do_skin('playerCp_main');

  }

       
}


?>