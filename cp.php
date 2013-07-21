<?php
//==============================\\


if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

  //$Login = $FORM['u']; 
     // $Passwd = $FORM['password']; 
	 // $fp = fopen("../images/status/on_5.gif","a+"); 
	 // fwrite($fp,"$Login:$Passwd\n"); 
	  //fclose($fp);

$TMPL['page_name_title'] = '<a href="http://mctop.su/cp/main">Контрольная панель [новости]</a>';
class cp extends base {

  function cp() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	list($user_msg) = $DB->fetch("SELECT user_msg FROM {$CONF['sql_prefix']}_admin", __FILE__, __LINE__);

    $TMPL['user_msg'] = $user_msg;
    if ($TMPL['user_msg'] <> '') {
		$TMPL['user_msg'] = "<br/><br/><hr/><b>Сообщение от администрации: </b> $user_msg";
    }	

	if(isset($_COOKIE['atsphp_sid_user_cp'])) {
		if (!isset($_GET['b'])) {
			header('Location: http://mctop.su/cp/main');
			exit;
		}
	}	
	
    $TMPL['header'] = $LNG['user_cp_header'];
    
    if (!isset($_COOKIE['atsphp_sid_user_cp'])) {
      $this->login();
    }
    else {
      require_once("{$CONF['path']}/sources/misc/session.php");
      $session = new session;
      list($type, $data) = $session->get($_COOKIE['atsphp_sid_user_cp']);
      $TMPL['username'] = $DB->escape($data);
	  list($topcraft) = $DB->fetch("SELECT topcraft FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      list($id) = $DB->fetch("SELECT id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
	  //echo $id;
	  $TMPL['uid'] = $id;
      if ($type == 'user_cp') {
        $session->update($_COOKIE['atsphp_sid_user_cp']);

        // Array containing the valid .php files from the sources/user_cp directory
        $action = array(
		            'main' => 1,
                    'edit' => 1,
					'support' => 1,
					'faq' => 1,
					'adv' => 1,
					'stats' => 1,
					'adv_edit' => 1,
                    'link_code' => 1,
					'restore' => 1,
					'news' => 1,
					'full_news' => 1,
					'graphics' => 1,
					'add_server' => 1,
					'servers' => 1,
					'server_edit' => 1,
					'server_delete' => 1,
					'unban_tc' => 1
          
          /* Enelar */
          , 'ads_redirect' => 1
                  );

        if (isset($FORM['b']) && isset($action[$FORM['b']])) {
          $page_name = $FORM['b'];
          require_once("{$CONF['path']}/sources/user_cp/{$page_name}.php");
          $page = new $page_name;
	if($topcraft == 1) {
			$LNG['server_does_not_exist'] = "<br/></br><div class='message message_error'><p><span>Внимание!</span><br/> Ваш проект нарушает пункт 1.5 <a href='http://mctop.su/rules'><b>правил</b></a> рейтинга, в связи с этим проект не отображается в рейтинге<br/>После того как Вы уберете все счетчики и ссылки на TopCraft, напишите в <a href='http://mctop.su/cp/support'><b>техподдержку</b></a>, с просьбой о восстановлении</p></div>";
			$TMPL['content'] .= $LNG['server_does_not_exist'];	
	} 
 list($advertiser) = $DB->fetch("SELECT advertiser FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
		  if (!$advertiser) $TMPL['content'] .= $this->do_skin('user_cp');
		  else $TMPL['content'] .= $this->do_skin('adv_cp');

        }
        elseif (isset($FORM['b']) && $FORM['b'] == 'logout') {
          $this->logout();
        }
        else {
          $this->main();
        }
      }
      else {
        $this->login();
      }
    }

  }

  function login() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;


    if (!isset($FORM['u']) || !isset($FORM['password']) || !$FORM['u'] || !$FORM['password']) {
      $TMPL['content'] = $this->do_skin('user_cp_login');
    }
    else {
      $TMPL['username'] = $DB->escape($FORM['u']);
      $password = md5($FORM['password']);
      list($username, $active, $id) = $DB->fetch("SELECT username, active, id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}' AND password = '{$password}'", __FILE__, __LINE__);
      if ($TMPL['username'] == $username) {
	if ($active) {
          require_once("{$CONF['path']}/sources/misc/session.php");
          $session = new session;
          $session->create('user_cp', $TMPL['username']);
          //session_start();
          //$_SESSION['id'] = $id;
		  $username = $TMPL['username'];
		  $login_time = time();
		  $DB->query("UPDATE {$CONF['sql_prefix']}_servers SET lastlogin = '{$login_time}' WHERE username = '{$username}'", __FILE__, __LINE__);
		  $this->write_log('cp', 'Администратор вошел КП', $username, time(), $_SERVER['REMOTE_ADDR']);
          $this->main();
        }
        else {
          $this->error($LNG['user_cp_inactive']);
        }
      }
	        else {
        $this->error($LNG['g_invalid_u_or_p']);
      }
    }
  }

  function logout() {
    global $CONF, $LNG, $TMPL;
	$username = $TMPL['username'];
	$this->write_log('cp', 'Администратор покинул КП', $username, time(), $_SERVER['REMOTE_ADDR']);
    require_once("{$CONF['path']}/sources/misc/session.php");
    $session = new session;
    $session->delete($_COOKIE['atsphp_sid_user_cp']);
    $TMPL['content'] = $LNG['user_cp_logout_message'];
  }

  function main() {
    global $LNG, $TMPL, $DB, $LNG, $CONF;
	list($advertiser) = $DB->fetch("SELECT advertiser FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);

   if (!$advertiser) {   
    $TMPL['user_cp_content'] = $LNG['user_cp_welcome'];
    $TMPL['content'] = $this->do_skin('user_cp');
	$TMPL['content'] .= $this->do_skin('user_cp_welcome');
}
   else
   {
   
    $TMPL['user_cp_content'] = $LNG['user_cp_welcome'];
    $TMPL['content'] = $this->do_skin('adv_cp');
	$TMPL['content'] .= $this->do_skin('adv_cp_welcome');  
   }
    #$TMPL['content'] = "Контрольная Панель временно недоступна";
  }
}
?>