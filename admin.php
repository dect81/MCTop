<?php
//==============================\\
//             L.R.E            \\
//     Created by RaM Team      \\
//==============================\\ 

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}
//$TMPL['page_name_title'] = 'Панель администратора';

class admin extends base {


  function admin() {
    global $CONF, $FORM, $LNG, $TMPL;
//echo $_SERVER['REMOTE_ADDR'];
	$TMPL['admin'] = 1;
	//
    $TMPL['version'] = $CONF['version'];
    $TMPL['header'] = $LNG['a_header'];

    if (!isset($_COOKIE['atsphp_sid_admin'])) {
      $this->login();
	  }
    else {

      require_once("{$CONF['path']}/sources/misc/session.php");
      $session = new session;
      list($type, $data) = $session->get($_COOKIE['atsphp_sid_admin']);
      if ($type == 'admin') {
        $session->update($_COOKIE['atsphp_sid_admin']);
      
        // Array containing the valid .php files from the sources/admin directory
        $action = array(
                    'main' => 1,
                    'approve' => 1,
                    'fixvote' => 1,
                    'approve_edited' => 1,
                    'approve_reviews' => 1,
                    'backup_database' => 1,
                    'create_page' => 1,
                    'delete' => 1,
                    'delete_bad_word' => 1,
                    'delete_ban' => 1,
                    'delete_page' => 1,
                    'delete_review' => 1,
                    'edit' => 1,
                    'edit_news' => 1,
                    'edit_page' => 1,
                    'edit_bad_word' => 1,
                    'edit_ban' => 1,
                    'edit_review' => 1,
                    'email' => 1,
                    'manage' => 1,
                    'manage_bad_words' => 1,
                    'manage_ban' => 1,
                    'manage_pages' => 1,
                    'manage_reviews' => 1,
                    'manage_news' => 1,
                    'settings' => 1,
                    'skins' => 1,
					'features' => 1,
					'manage_ap' => 1,
					'new_admin' => 1,
					'banned' => 1,
					'inactive' => 1,
					'offline' => 1,
					'create_news' => 1,
					'create_adm_news' => 1,
					'logs' => 1,
					'reviews_search' => 1,
					'realtime' => 1,
					'approve_new' => 1,
					'edit_new' => 1,
					'delete_new' => 1,
                    'rating_stats' => 1
                  );

        if (isset($FORM['b']) && isset($action[$FORM['b']])) {
          $page_name = $FORM['b'];
          require_once("{$CONF['path']}/sources/admin/{$page_name}.php");
          $page = new $page_name;

          $TMPL['content'] = $this->do_skin('admin');
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
      $TMPL['content'] = $this->do_skin('admin_login');
    }
    else {
      $TMPL['username'] = $DB->escape($FORM['u']);
      $password = md5($FORM['password']);
      list($username, $active) = $DB->fetch("SELECT admin FROM {$CONF['sql_prefix']}_admins WHERE admin = '{$TMPL['username']}' AND password = '{$password}'", __FILE__, __LINE__);
      if ($TMPL['username'] == $username) {
          require_once("{$CONF['path']}/sources/misc/session.php");
          $session = new session;
          $session->create('admin', $TMPL['username']);
          setcookie('lrengine_un', $TMPL['username'], time()+604800);
          $TMPL['content'] = $this->do_skin('admin_panel_hack');
		  $this->write_log('admin', 'Администратор вошел в АП', $TMPL['username'], time(), $_SERVER['REMOTE_ADDR']);
      }
      else {
        $this->error($LNG['g_invalid_u_or_p']);
      }
    }
  }

  function logout() {
    global $CONF, $LNG, $TMPL, $DB;
	//админ вышел:(
	//$DB->query("UPDATE {$CONF['sql_prefix']}_admin SET `admins_online` = admins_online - 1", __FILE__, __LINE__);
    require_once("{$CONF['path']}/sources/misc/session.php");
    $session = new session;
    $session->delete($_COOKIE['atsphp_sid_admin']);
	$username = $_COOKIE['lrengine_un'];
	$this->write_log('admin', 'Администратор покинул АП', $username, time(), $_SERVER['REMOTE_ADDR']);
    setcookie('lrengine_un', $TMPL['username'], time()-99999);

    $TMPL['content'] = $LNG['a_logout_message'];
  }

  function main() {
    global $DB, $CONF, $LNG, $TMPL;


	$TMPL['engine_version'] = '1.3.1';
	list($adm_msg) = $DB->fetch("SELECT adm_msg FROM {$CONF['sql_prefix']}_admin", __FILE__, __LINE__);
	list($msg_by) = $DB->fetch("SELECT admin FROM {$CONF['sql_prefix']}_admin", __FILE__, __LINE__);
	if(!$adm_msg or empty($adm_msg)) {
	$TMPL['message'] = "";
	} else {
	$TMPL['message'] = "$msg_by: $adm_msg";
	}

	
    $phpversion = phpversion();

$latest_version = '1.1.2';
$admin_footer = <<<HTML

HTML;



	list($num_waiting_online) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers WHERE active = 2 and status = 1 and (success/attemps*100) > 30", __FILE__, __LINE__);
    #Новые сервера
    list($num_waiting) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers_real WHERE active = 0", __FILE__, __LINE__);
    if ($num_waiting == 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/admin/u/approve\">{$LNG['a_main_approve']}</a><hr size='1' width = '250' align='left'>";
    }
    elseif ($num_waiting > 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/admin/u/approve\">".sprintf($LNG['a_main_approves'], $num_waiting)."</a><hr size='1' width = '250' align='left'>";
    }
    #Недоступные серверы
    list($num_offline) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers WHERE status = 0 and advertiser = 0 and username <> 'Helper' ", __FILE__, __LINE__);
	if ($num_offline == 1) {
	
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/admin/u/offline\">1 недоступный сервер</a><hr size='1' width = '250' align='left'>";
    }	
    elseif ($num_offline > 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/admin/u/offline\">Недоступные серверы (".sprintf($num_offline).")</a><hr size='1' width = '250' align='left'>";
    }
	#Неактивные сервера
	    list($num_inactive) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers WHERE active = 2 and username <> 'Helper' ", __FILE__, __LINE__);

    if ($num_inactive == 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/admin/u/inactive\">{$LNG['a_inactive_serv']}</a><hr size='1' width = '250' align='left'><font size='-3'> [Готовых: {$num_waiting_online}]</font>";
    }
    elseif ($num_inactive > 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/admin/u/inactive\">".sprintf($LNG['a_inactive_servs'], $num_inactive)."</a><font size='-3'> [Готовых: {$num_waiting_online}]</font><hr size='1' width = '250' align='left'>";
    }
	//
	#Заблокированные сервера
	    list($num_waiting) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers WHERE active = 3", __FILE__, __LINE__);
    if ($num_waiting == 1) {
      $TMPL['admin_content'] .= "<font color='red'>{$LNG['a_block_serv']} <a href=\"{$TMPL['list_url']}/index.php?a=admin&amp;b=banned\">[перейти]</a><hr size='1' width = '250' align='left'></font>";
    }
    elseif ($num_waiting > 1) {
      $TMPL['admin_content'] .= "<font color='red'>".sprintf($LNG['a_block_servs'], $num_waiting)."</font><a href=\"{$TMPL['list_url']}/index.php?a=admin&amp;b=banned\">[перейти]</a><hr size='1' width = '250' align='left'>";
    }
	//
	
    list($num_waiting_edited) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_servers_edited", __FILE__, __LINE__);
    if ($num_waiting_edited == 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/index.php?a=admin&amp;b=approve_edited\">{$LNG['a_main_approve_edit']}</a><hr size='1' width = '250' align='left'>";
    }
    elseif ($num_waiting_edited > 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/index.php?a=admin&amp;b=approve_edited\">".sprintf($LNG['a_main_approve_edits'], $num_waiting_edit)."</a><hr size='1' width = '250' align='left'>";
    }

    list($num_waiting_rev) = $DB->fetch("SELECT COUNT(*) FROM {$CONF['sql_prefix']}_reviews WHERE active = 0", __FILE__, __LINE__);
    if ($num_waiting_rev == 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/index.php?a=admin&amp;b=approve_reviews\">{$LNG['a_main_approve_rev']}</a><hr size='1' width = '250' align='left'>";
    }
    elseif ($num_waiting_rev > 1) {
      $TMPL['admin_content'] .= "<a href=\"{$TMPL['list_url']}/index.php?a=admin&amp;b=approve_reviews\">".sprintf($LNG['a_main_approve_revs'], $num_waiting_rev)."</a><hr size='1' width = '250' align='left'>";
    }
    //$TMPL['admin_content'] .= "$rasst {$LNG['a_main_your']}: {$TMPL['version']}{$LNG['a_main_latest']}: {$latest_version}\n{$LNG['a_main_new']}";
    $TMPL['admin_content'] .= "$admin_footer";
    $TMPL['content'] = $this->do_skin('admin');
    #$TMPL['content'] = "Ололо";
  }
}
?>
