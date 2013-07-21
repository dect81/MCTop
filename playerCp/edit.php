<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class edit extends join_edit {
  function edit() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['edit_header'];
    $TMPL['uid'] = $_COOKIE['uid'];

    if (!isset($FORM['submit'])) {
      //$TMPL['user_cp_content'] = 'Under construction';
	  $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;

    if (!isset($TMPL['url'])) {
      $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_users WHERE vk_id = '{$TMPL['uid']}'", __FILE__, __LINE__);
      $TMPL = array_merge($TMPL, $row);

    }
    else {
      if (isset($TMPL['id'])) { $TMPL['id'] = stripslashes($TMPL['id']); }
      if (isset($TMPL['name'])) { $TMPL['name'] = stripslashes($TMPL['name']); }
      if (isset($TMPL['server'])) { $TMPL['server'] = stripslashes($TMPL['server']); }
 
    }



    $TMPL['id'] = htmlspecialchars($TMPL['id']);
    $TMPL['name'] = htmlspecialchars($TMPL['name']);
    $TMPL['server'] = htmlspecialchars($TMPL['server']);


    $TMPL['user_cp_content'] = $this->do_skin('playerCp_edit');


  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['id'] = $DB->escape($FORM['id'], 1);
    $TMPL['name'] = $DB->escape($FORM['name'], 1);
    $TMPL['server'] = $DB->escape($FORM['server'], 1);




        // Update everything but URL and title
		//$username = $TMPL['username'];
		/*list($title, $description, $det_description, $banner_url, $email, $password, $url, $serv_version, $serv_ip, $serv_port) = $DB->fetch("SELECT title, description, det_description, banner_url, email, password, url, serv_version, serv_ip, serv_port  FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
		$form = <<<HTML
		Администратор сменил инорфмацию о сервере <input type='button' value='Развернуть' class='input-button' onclick='if (this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display != '') { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = '';	 this.innerText = ''; this.value = 'Свернуть'; } else { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = 'none'; this.innerText = ''; this.value = 'Развернуть'; }'/>
</div>
<div class='alt2'>
<div style='display: none;'>
<table>
<tr><td><input size=\'50\' value =\'{$title}\'/></td> <td><input size=\'50\' value =\'{$TMPL['title']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$description}\'/></td> <td><input size=\'50\' value =\'{$TMPL['description']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$det_description}\'/></td> <td><input size=\'50\' value =\'{$TMPL['det_description']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$banner_url}\'/></td> <td><input size=\'50\' value =\'{$TMPL['banner_url']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$email}\'/></td> <td><input size=\'50\' value =\'{$TMPL['email']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$password}\'/></td> <td><input size=\'50\' value =\'{$TMPL['password']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$url}\'/></td> <td><input size=\'50\' value =\'{$TMPL['password']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$serv_version}\'/></td> <td><input size=\'50\' value =\'{$TMPL['serv_version']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$serv_ip}\'/></td> <td><input size=\'50\' value =\'{$TMPL['serv_ip']}\'/></td> <tr/>		
<tr><td><br/> <input size=\'50\' value =\'{$serv_port}\'/></td> <td><input size=\'50\' value =\'{$TMPL['serv_port']}\'/></td>  <tr/>			
</table>
</div>
</div>
</div>
HTML;
		
		
		//$this->write_log('cp_edit', $form, $username, time(), $_SERVER['REMOTE_ADDR']);*/
		$query = "UPDATE {$CONF['sql_prefix']}_users SET server = '{$TMPL['server']}' WHERE vk_id = {$TMPL['uid']}";
		//echo $query;
        $DB->query($query, __FILE__, __LINE__);

        

        $TMPL['user_cp_content'] = $this->do_skin('playerCp_edit_finish');
      }
  }
?>
