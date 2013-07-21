<?php
//==============================\\


if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}



$TMPL[page_name_title] = 'Личный кабинет';
class playerCp extends base {
  function playerCp() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
    $uid = $_COOKIE['uid'];
	if(isset($_COOKIE['uid'])) {
		if (!isset($_GET['b'])) {
		
			header('Location: http://mctop.su/playerCp/main');
			exit;
		}
	}	
	
    $TMPL['header'] = $TMPL[page_name_title];

    if (isset($_COOKIE['uid'])&&isset($_COOKIE['pass'])) {
	
      require_once("{$CONF['path']}/sources/misc/session.php");
      $session = new session;
      list($type, $data) = $session->get($_COOKIE['atsphp_sid_user_cp']);
      $TMPL['username'] = $DB->escape($data);
      $TMPL['uid'] = $_COOKIE['uid'];
      

      list($id) = $DB->fetch("SELECT id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      list($aid) = $DB->fetch("SELECT id FROM {$CONF['sql_prefix']}_users WHERE vk_id = '{$uid}'", __FILE__, __LINE__);
      list($vk_id) = $DB->fetch("SELECT vk_id FROM {$CONF['sql_prefix']}_users WHERE vk_id = '{$uid}'", __FILE__, __LINE__);
	  list($name) = $DB->fetch("SELECT name FROM {$CONF['sql_prefix']}_users WHERE vk_id = '{$uid}'", __FILE__, __LINE__);
	  //echo $id;
	  $TMPL['uid'] = $id;
	  $TMPL['name'] = $name;
	  $TMPL['aid'] = $aid;
	  $TMPL['vk_id'] = $vk_id;

        $session->update($_COOKIE['atsphp_sid_user_cp']);

        // Array containing the valid .php files from the sources/user_cp directory
        $action = array(
		            'main' => 1,
                    'edit' => 1,
					'support' => 1,
					'faq' => 1,
					'votes' => 1,
					'stats' => 1,
					'vote' => 1,
                    'link_code' => 1,
					'restore' => 1,
					'mods' => 1,
					'full_mod' => 1,
					'tour' => 1
                  );

        if (isset($FORM['b']) && isset($action[$FORM['b']])) {
          $page_name = $FORM['b'];
          require_once("{$CONF['path']}/sources/playerCp/{$page_name}.php");
          $page = new $page_name;
		  $TMPL['content'] = $this->do_skin('playerCp');


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

  function login() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

	session_start();
	$_SESSION["rtt_playerCp_login"] = "1";

	$TMPL['content'] = $this->do_skin('playerCp_login');
  
  }

  function logout() {
    global $CONF, $LNG, $TMPL;
	$username = $TMPL['username'];
	$this->write_log('playerCp', 'Игрок покинул КП', $uid, time(), $_SERVER['REMOTE_ADDR']);
    setcookie('uid','1', time()-(60*60*24*30),'/');
    setcookie('pass','1', time()-(60*60*24*30),'/');
    $TMPL['content'] = $LNG['user_cp_logout_message'];
  }

  function main() {
    global $LNG, $TMPL, $DB, $LNG, $CONF;
 
    $TMPL['user_cp_content'] = $LNG['playerCp_welcome'];
    $TMPL['content'] = $this->do_skin('playerCp');
	$TMPL['content'] .= $this->do_skin('playerCp_welcome');


  }
}
?>