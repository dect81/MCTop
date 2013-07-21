<?php
//==============================\\


if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}
$TMPL[page_name_title] = 'Регистрация';
class reg extends join_edit {
  function reg() {
    global $FORM, $LNG, $TMPL;

	$TMPL['register'] = 1;
    $TMPL['header'] = $LNG['join_header'];

    $TMPL['error_username'] = '';
    $TMPL['error_password'] = '';
    $TMPL['error_url'] = '';
    $TMPL['error_email'] = '';
    $TMPL['error_title'] = '';
    $TMPL['error_banner_url'] = '';
    $TMPL['error_top'] = '';
    $TMPL['error_captcha'] = '';
    $TMPL['error_question'] = '';
    $TMPL['error_av_online'] = '';
    $TMPL['error_serv_version'] = '';
    $TMPL['error_serv_ip'] = '';
    $TMPL['error_email_duplicate'] = '';
    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $FORM, $LNG, $TMPL;

    // Display the CAPTCHA?
    if ($CONF['captcha']) {
      $TMPL['rand'] = rand(1, 1000000);
      $TMPL['join_captcha'] = $this->do_skin('join_captcha');
    }
    else {
      $TMPL['join_captcha'] = '';
    }

    // Display the security question?
    if ($CONF['security_question'] != '' && $CONF['security_answer'] != '') {
      $TMPL['security_question'] = $CONF['security_question'];
      if (isset($FORM['security_answer'])) { $TMPL['security_answer'] = strip_tags($FORM['security_answer']); }
      else { $TMPL['security_answer'] = ''; }

      $TMPL['join_question'] = $this->do_skin('join_question');
    }
    else {
      $TMPL['join_question'] = '';
    }

    $TMPL['whitelist_menu'] = "<select name=\"whitelist\">\n";
    if ($TMPL['whitelist'] == 0) {
      $TMPL['whitelist_menu'] .= "<option value=\"0\" selected=\"selected\">Выключен</option>\n<option value=\"1\">Включен</option>\n";
    }
    if ($TMPL['whitelist'] == 1) {
      $TMPL['whitelist_menu'] .= "<option value=\"1\" selected=\"selected\">Включен</option>\n\n<option value=\"0\">Выключен</option>\n";
    }
    $TMPL['whitelist_menu'] .= '</select>';
	
	$TMPL['clienttype_menu'] = "<select name=\"clienttype\">\n";
    if ($TMPL['clienttype'] == 0) {
      $TMPL['clienttype_menu'] .= "<option value=\"0\" selected=\"selected\">Пиратка</option>\n<option value=\"1\">Лицензия</option>\n";
    }
    if ($TMPL['clienttype'] == 1) {
      $TMPL['clienttype_menu'] .= "<option value=\"1\" selected=\"selected\">Лицензия</option>\n\n<option value=\"0\">Пиратка</option>\n";
    }
    $TMPL['clienttype_menu'] .= '</select>';

    if ($CONF['max_banner_width'] && $CONF['max_banner_height']) {
      $TMPL['join_banner_size'] = sprintf($LNG['join_banner_size'], $CONF['max_banner_width'], $CONF['max_banner_height']);
    }
    else {
      $TMPL['join_banner_size'] = '';
    }

    if (!isset($TMPL['username'])) { $TMPL['username'] = ''; }
    if (!isset($TMPL['url'])) { $TMPL['url'] = 'http://'; }
    if (!isset($TMPL['title'])) { $TMPL['title'] = ''; }
    if (!isset($TMPL['description'])) { $TMPL['description'] = ''; }
    if (!isset($TMPL['banner_url'])) { $TMPL['banner_url'] = 'http://'; }
    if (!isset($TMPL['email'])) { $TMPL['email'] = ''; }
    if (!isset($TMPL['av_online'])) { $TMPL['av_online'] = ''; }
    if (!isset($TMPL['serv_version'])) { $TMPL['serv_version'] = ''; }
    if (!isset($TMPL['ip'])) { $TMPL['ip'] = ''; }
    if (!isset($TMPL['port'])) { $TMPL['port'] = '25565'; }


    if (isset($TMPL['url'])) { $TMPL['url'] = stripslashes($TMPL['url']); }
    if (isset($TMPL['title'])) { $TMPL['title'] = stripslashes($TMPL['title']); }
    if (isset($TMPL['description'])) { $TMPL['description'] = stripslashes($TMPL['description']); }
    if (isset($TMPL['clienttype'])) { $TMPL['clienttype'] = stripslashes($TMPL['clienttype']); }
    if (isset($TMPL['category'])) { $TMPL['category'] = stripslashes($TMPL['category']); }
    if (isset($TMPL['banner_url'])) { $TMPL['banner_url'] = stripslashes($TMPL['banner_url']); }
    if (isset($TMPL['email'])) { $TMPL['email'] = stripslashes($TMPL['email']); }
    if (isset($TMPL['av_online'])) { $TMPL['av_online'] = (int)$TMPL['av_online']; }
    if (isset($TMPL['serv_version'])) { $TMPL['serv_version'] = stripslashes($TMPL['serv_version']); }
    if (isset($TMPL['ip'])) { $TMPL['ip'] = stripslashes($TMPL['ip']); }
    if (isset($TMPL['port'])) { $TMPL['port'] = (int)$TMPL['port']; }
    
    $TMPL['content'] = $this->do_skin('join_form');
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['username'] = $DB->escape($FORM['u'], 1);
    $TMPL['url'] = $DB->escape($FORM['url'], 1);
    $TMPL['title'] = $DB->escape($FORM['title'], 1);
    $FORM['description'] = str_replace(array("\r\n", "\n", "\r"), ' ', $FORM['description']);
    $TMPL['description'] = $DB->escape($FORM['description'], 1);

    $TMPL['clienttype'] = $DB->escape($FORM['clienttype'], 1);
    $TMPL['category'] = $DB->escape($FORM['category'], 1);
    $TMPL['banner_url'] = $DB->escape($FORM['banner_url'], 1);
    $TMPL['email'] = $DB->escape($FORM['email'], 1);
    $TMPL['av_online'] = $DB->escape($FORM['av_online'], 1);
    $TMPL['serv_version'] = $DB->escape($FORM['serv_version'], 1);
    $TMPL['ip'] = $DB->escape($FORM['ip'], 1);
    $TMPL['port'] = $DB->escape($FORM['port'], 1);
    
    $TMPL['title'] = $this->bad_words($TMPL['title']);
    $TMPL['description'] = $this->bad_words($TMPL['description']);
	$TMPL['whitelist'] = intval($FORM['whitelist']);
	$TMPL['clienttype'] = intval($FORM['clienttype']);
	

    if ($this->check_ban('join')) {

      if ($this->check_input('join')) {
	
        $password = md5($FORM['password']);

 //       require_once("{$CONF['path']}/sources/in.php");
//        $short_url = in::short_url($TMPL['url']);

        $join_date = date('Y-m-d', time() + (3600*$CONF['time_offset']));


		
        $user_ip = $DB->escape($_SERVER['REMOTE_ADDR'], 1);
         list($aid) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_servers", __FILE__, __LINE__);
         if (!$aid) {$aid = 1;}
		$lastlogin = time();
		
       $DB->query("INSERT INTO {$CONF['sql_prefix']}_servers (id, username, serv_ip, serv_port, password, url, title, category, banner_url, email, join_date, active, openid, user_ip, status, lastlogin)
                  VALUES ({$aid}, '{$TMPL['username']}', '{$TMPL['ip']}', '{$TMPL['port']}', '{$password}', '{$TMPL['url']}', '{$TMPL['title']}', '{$TMPL['category']}', '{$TMPL['banner_url']}', '{$TMPL['email']}', '{$join_date}', {$CONF['active_default']}, 0, '{$user_ip}', '2','{$lastlogin}')", __FILE__, __LINE__);

		list($id) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_servers_real", __FILE__, __LINE__);    

       $DB->query("INSERT INTO {$CONF['sql_prefix']}_servers_real (id, uid, ip, port, title, description, version, whitelist, clienttype, username, active, main_serv, status)
                  VALUES ({$id}, '{$aid}', '{$TMPL['ip']}', '{$TMPL['port']}', '{$TMPL['title']}', '{$TMPL['description']}', '{$TMPL['serv_version']}', '{$TMPL['whitelist']}','{$TMPL['clienttype']}','{$TMPL['username']}','0','1','2')", __FILE__, __LINE__);
        
		$DB->query("INSERT INTO {$CONF['sql_prefix']}_stats (id, username) VALUES ({$aid}, '{$TMPL['username']}')", __FILE__, __LINE__);
		
		list($id) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_graphics_data", __FILE__, __LINE__);
        $DB->query("INSERT INTO {$CONF['sql_prefix']}_graphics_data (id, pid, username) VALUES ({$id}, {$aid}, '{$TMPL['username']}')", __FILE__, __LINE__);

        if ($CONF['google_friendly_links']) {
          $TMPL['verbose_link'] = "";
        }
        else {
          $TMPL['verbose_link'] = "index.php?act=in&u={$TMPL['username']}";
        }
        $TMPL['link_code'] = $this->do_skin('link_code');

        $LNG['join_welcome'] = sprintf($LNG['join_welcome'], $TMPL['list_name']);

        if ($CONF['email_admin_on_join']) {
          $join_email_admin = new skin('join_email_admin');
          $join_email_admin->send_email($CONF['your_email']);
        }

        if ($CONF['active_default']) {
          $join_email = new skin('join_email');
          $join_email->send_email($TMPL['email']);

          $TMPL['content'] = $this->do_skin('join_finish');
        }
        else {
          $TMPL['content'] = $this->do_skin('join_finish_approve');
        }
      }
      else {
        $this->form();
      }
    }
    else {
      $this->form();
    }
  }
}
?>
