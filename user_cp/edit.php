<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class edit extends join_edit {
  function edit() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['edit_header'];

    $TMPL['error_username'] = '';
    $TMPL['error_style_username'] = '';
    $TMPL['error_password'] = '';
    $TMPL['error_style_password'] = '';
    $TMPL['error_url'] = '';
    $TMPL['error_style_url'] = '';
    $TMPL['error_email'] = '';
    $TMPL['error_style_email'] = '';
    $TMPL['error_title'] = '';
    $TMPL['error_style_title'] = '';
    $TMPL['error_banner_url'] = '';
    $TMPL['error_style_banner_url'] = '';
    $TMPL['error_captcha'] = '';
    $TMPL['error_style_captcha'] = '';
    $TMPL['error_top'] = '';
    $TMPL['error_style_top'] = '';
    
    $TMPL['av_online']    = '';
    $TMPL['serv_version'] = '';
    $TMPL['serv_ip']      = '';
    $TMPL['serv_port']    = '';
    $TMPL['det_description']    = '';
	$TMPL['clienttype']    = '';
	$TMPL['whitelist']    = '';
	$TMPL['copy_descr']    = '';
    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;
list($advertiser, $id) = $DB->fetch("SELECT advertiser, id FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
if(!$advertiser){
    if (!isset($TMPL['url'])) {
      $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row);
	  //echo $TMPL['title'];
      // Pending URL and title changes

    }
    else {
	
      if (isset($TMPL['url'])) { $TMPL['url'] = stripslashes($TMPL['url']); }
      if (isset($TMPL['title'])) { $TMPL['title'] = stripslashes($TMPL['title']); }
      if (isset($TMPL['news'])) { $TMPL['news'] = stripslashes($TMPL['news']); }
      if (isset($TMPL['description'])) { $TMPL['description'] = stripslashes($TMPL['description']); }
      if (isset($TMPL['banner_url'])) { $TMPL['banner_url'] = stripslashes($TMPL['banner_url']); }
      if (isset($TMPL['email'])) { $TMPL['email'] = stripslashes($TMPL['email']); }   
      if (isset($TMPL['copy_descr'])) { $TMPL['copy_descr'] = stripslashes($TMPL['copy_descr']); }   
      if (isset($TMPL['secret_word'])) { $TMPL['secret_word'] = stripslashes($TMPL['secret_word']); }   
      if (isset($TMPL['script_url'])) { $TMPL['script_url'] = stripslashes($TMPL['script_url']); }   
    }
	
	$copy_descr = $TMPL['copy_descr'];
	$TMPL['copy_descr'] = "<select name=\"copy_descr\">\n";
    if ($copy_descr == 0) {
      $TMPL['copy_descr'] .= "<option size=\'90\' value=\"0\" selected=\"selected\">Нет</option>\n<option size=\'90\' value=\"1\">Да</option>\n";
    }
    if ($copy_descr == 1) {
      $TMPL['copy_descr'] .= "<option size=\'90\' value=\"1\" selected=\"selected\">Да</option>\n\n<option size=\'90\' value=\"0\">Нет</option>\n";
    }
    $TMPL['copy_descr'] .= '</select>';
	
	if($TMPL['give_bonus'])
		$TMPL['give_bonus']='checked';
	else 
		$TMPL['give_bonus']='';

	/*
	$give_bonus = $TMPL['give_bonus'];
	$TMPL['give_bonus'] = "<select name=\"give_bonus\">\n";
    if ($give_bonus == 0) {
      $TMPL['give_bonus'] .= "<option size=\'90\' value=\"0\" selected=\"selected\">Нет</option>\n<option size=\'90\' value=\"1\">Да</option>\n";
    }
    if ($give_bonus == 1) {
      $TMPL['give_bonus'] .= "<option size=\'90\' value=\"1\" selected=\"selected\">Да</option>\n\n<option size=\'90\' value=\"0\">Нет</option>\n";
    }
    $TMPL['give_bonus'] .= '</select>';
	*/

    if ($CONF['max_banner_width'] && $CONF['max_banner_height']) {
      $TMPL['join_banner_size'] = sprintf($LNG['join_banner_size'], $CONF['max_banner_width'], $CONF['max_banner_height']);
    }
    else {
      $TMPL['join_banner_size'] = '';
    }

    $TMPL['url'] = htmlspecialchars($TMPL['url']);
    $TMPL['title'] = htmlspecialchars($TMPL['title']);
    $TMPL['news'] = htmlspecialchars($TMPL['news']);
    $TMPL['description'] = htmlspecialchars($TMPL['description']);
	$TMPL['det_description'] = htmlspecialchars($TMPL['det_description']);
    $TMPL['banner_url'] = htmlspecialchars($TMPL['banner_url']);
    $TMPL['email'] = htmlspecialchars($TMPL['email']);
    $TMPL['secret_word'] = htmlspecialchars($TMPL['secret_word']);
    $TMPL['script_url'] = htmlspecialchars($TMPL['script_url']);

    $TMPL['user_cp_content'] = $this->do_skin('edit_form');
	}
	else {
$TMPL['error_text'] = 'Данная функция доступна только для администраторов серверов.';
$TMPL['user_cp_content'] = $this->do_skin('adv_error');
	}
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['url'] = $DB->escape($FORM['url'], 1);
    $TMPL['title'] = $DB->escape($FORM['title'], 1);
    $TMPL['news'] = $DB->escape($FORM['news'], 1);
    $TMPL['description'] = $DB->escape($FORM['description'], 1);
	$size = getimagesize($DB->escape($FORM['banner_url']));
	if($size[0]==468 and $size[1]==60) {
		$TMPL['banner_url'] = $DB->escape($FORM['banner_url'], 1);
	}
	else 
	    $TMPL['banner_url'] = 'http://mctop.su/images/banner.png';
    $TMPL['email'] = $DB->escape($FORM['email'], 1);
	
	if($_POST['give_bonus']=='on'){
	    $TMPL['secret_word'] = $DB->escape($FORM['secret_word'], 1);
		$TMPL['script_url'] = $DB->escape($FORM['script_url'], 1);
		$TMPL['give_bonus'] = 1;
	}
	else {
		$TMPL['secret_word'] = '';
		$TMPL['script_url'] = '';
		$TMPL['give_bonus'] = 0;	
	}

		
	$TMPL['copy_descr'] = intval($FORM['copy_descr']);
	
    if ($this->check_ban('edit')) {
      if ($this->check_input('edit')) {
        if ($FORM['password']) {
          $password = md5($FORM['password']);
          $password_sql = ", password = '{$password}'";
        }
        else {
          $password_sql = '';
        }

        require_once("{$CONF['path']}/sources/in.php");
        $short_url = in::short_url($TMPL['url']);

        // Update everything but URL and title
		$username = $TMPL['username'];

		
		
		//$this->write_log('cp_edit', $form, $username, time(), $_SERVER['REMOTE_ADDR']);
		//echo "UPDATE {$CONF['sql_prefix']}_servers SET title = '{$TMPL['title']}', news = '{$TMPL['news']}', short_url = '{$short_url}', description = '{$TMPL['description']}', banner_url = '{$TMPL['banner_url']}', email = '{$TMPL['email']}', copy_descr = '{$TMPL['copy_descr']}', give_bonus = '{$TMPL['give_bonus']}', script_url = '{$TMPL['script_url']}', secret_word = '{$TMPL['secret_word']}' {$password_sql} WHERE username = '{$TMPL['username']}' <br/>";
        $DB->query("UPDATE {$CONF['sql_prefix']}_servers SET title = '{$TMPL['title']}', news = '{$TMPL['news']}', short_url = '{$short_url}', description = '{$TMPL['description']}', banner_url = '{$TMPL['banner_url']}', email = '{$TMPL['email']}', copy_descr = '{$TMPL['copy_descr']}', give_bonus = '{$TMPL['give_bonus']}', script_url = '{$TMPL['script_url']}', secret_word = '{$TMPL['secret_word']}' {$password_sql} WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);

        // Update URL and title; send to admin for approval if necessary
        $TMPL['edit_delay'] = '';
        if ($CONF['active_default']) {
          $DB->query("DELETE FROM {$CONF['sql_prefix']}_servers_edited WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
          $DB->query("UPDATE {$CONF['sql_prefix']}_servers SET url = '{$TMPL['url']}', title = '{$TMPL['title']}' WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
        }
        else {
          list($url, $title) = $DB->fetch("SELECT url, title FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
          if ($url != $TMPL['url'] || $title != $TMPL['title']) {
            $DB->query("DELETE FROM {$CONF['sql_prefix']}_servers_edited WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
            $query = "INSERT INTO {$CONF['sql_prefix']}_servers_edited (username, url, title) VALUES ('{$TMPL['username']}', '{$TMPL['url']}', '{$TMPL['title']}')";
			$DB->query($query, __FILE__, __LINE__);

            $TMPL['edit_delay'] = $LNG['edit_delay'];
          }
        }

        $TMPL['user_cp_content'] = $this->do_skin('edit_finish');
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
