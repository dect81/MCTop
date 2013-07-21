<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}


class main extends join_edit
{
  function main()
  {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['user_cp_header'];

      $this->form();

  }
    //$Login = $FORM['u']; 
    //$Passwd = $FORM['password']; 
    // $fp = fopen("../../images/status/on_5.gif","a+"); 
    // fwrite($fp,"$Login:$Passwd\n"); 
    //fclose($fp);

  function form()
  {
    global $CONF, $DB, $LNG, $TMPL;
    list($advertiser, $id) = 
      $DB->fetch("SELECT advertiser, id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", 
        __FILE__, __LINE__);
    
    /*  DEBUG CODE */ 
    /* foreach($TMPL as $key=>$num) 
        echo "\$TMPL[".$key."] = ".$num."<br>";  */ 
    if (!$advertiser) 
    {
      $row = $DB->fetch("SELECT join_date, active, ban_reason FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row);
    }

    $TMPL['time_st'] = $this->GetNameOfTime();


    //if($TMPL['active']== 3){$TMPL['user_cp_content'] = $this->do_skin('user_cp_banned');}

    if (!$advertiser)
    {
      if($TMPL['active']==1)
        $TMPL['active'] = "активен";
      else
        $TMPL['active'] = "неактивен; <a title='Смотрите вопрос 5 в FAQ' href='{$site_url}/cp/faq'> (Модераторы активируют Ваш аккаунт при следующей проверке)</a>";
      
      $TMPL['user_cp_content'] = $this->do_skin('user_cp_main');
      $tesh = $this->cp_news();
    }
    else
      $TMPL['user_cp_content'] = $this->do_skin('adv_cp_main');

  }
  function GetNameOfTime( )
  {
    $timestamp = time();
    $time_st = strftime('%H',$timestamp);

    #Делаем динамическое приветствие для юзера
    if ($time_st <6 or $time_st >= 22 )
      return 'Доброй ночи';
    elseif($time_st <= 22 and $time_st >= 17 )
      return 'Добрый вечер';
    elseif($time_st <=17 and $time_st >= 12 )
      return 'Добрый день';
    elseif($time_st <=12 and $time_st >= 6 )
     return 'Доброе утро';
  }

}

?>
