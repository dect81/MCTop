<?php
$timestamp = time();
$time_st = strftime('%H:%M:%S',$timestamp);
$u_cookie_id = '';
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class rate extends base {
   function rate() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['rate_header'];

    if (isset($FORM['u']) && $FORM['u']) {
        $TMPL['username'] = $DB->escape($FORM['u'], 1);
        $TMPL['nickname'] = $DB->escape($FORM['nickname'], 1);

		$username=$TMPL['username'];
        $ip  = $DB->escape($_SERVER['REMOTE_ADDR'], 1);
		        if(isset($_COOKIE['r_'.$username])) 
				{
				$test=$_COOKIE['r_'.$username];
				}
        $res = $DB->query("SELECT `id` FROM {$CONF['sql_prefix']}_votes WHERE `username` = '{$TMPL['username']}' AND hash = '{$test}' LIMIT 1", __FILE__, __LINE__);
        $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_votes WHERE `username` = '{$TMPL['username']}' AND hash = '{$test}'", __FILE__, __LINE__);
		
        $TMPL = array_merge($TMPL, (array)$row);

        if($DB->num_rows($res) == 1) {
            # Голосовал. Проверяем куки, при необходимости ставим $_COOKIE['r_'.$username]
            $check = $this->check_cookie($TMPL['username'], $ip);
            if($check == 1) 
			{  
    $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
    $TMPL = array_merge($TMPL, $row);

    $link = "<a href=\"{$TMPL['url']}\" onclick=\"out('{$TMPL['username']}');\">{$TMPL['title']}</a>";
    $TMPL['rate_message'] = sprintf($LNG['rate_message'], $link);
    
    $TMPL['rand'] = rand(1, 1000000);
    $TMPL['rate_captcha'] = $this->do_skin('join_captcha');
    
    $TMPL['content'] = $this->do_skin('rate_form');

                $ip = $DB->escape($_SERVER['REMOTE_ADDR'], 1);
                list($sid) = $DB->fetch("SELECT sid FROM {$CONF['sql_prefix']}_sessions WHERE type = 'captcha' AND data LIKE '{$ip}|%'", __FILE__, __LINE__);
                require_once("{$CONF['path']}/sources/misc/session.php");
                $session = new session;
                list($type, $data) = $session->get($sid);
                list($ip, $hash) = explode('|', $data);
                
                if (isset($FORM['captcha']) && $hash == sha1(')F*RJ@FHR^%X'.$FORM['captcha'].'(*Ht3h7f9&^F'.$ip)) {
                  $this->process($ip, $ip_sql);
                } else {
                  $this->form();    
                }
                
                $session->delete($sid);
			
            } else if($check == 3) {
                $TMPL['content'] = $this->do_skin('rate_not_finish');    
            } else if ($check == 2) {
                $this->set_cookie($TMPL['username'], $ip, time()+604800);
                $TMPL['content'] = $this->do_skin('rate_set');
            }       
        } else {
            # Не голосовал. Ставим куку, записываем в БД
            $this->set_cookie($TMPL['username'], $ip, time()+604800);
            $TMPL['content'] = $this->do_skin('rate_set');    
        }
    }  
  }
  
    function check_cookie($username, $ip) {
        global $CONF, $DB, $FORM, $LNG, $TMPL, $u_cookie_id;
        
        if(isset($_COOKIE['r_'.$username])) {
            # Проверка: засчитывать голос
            $time = time() - 60*60*24;
            $res = $DB->query("SELECT `id` FROM {$CONF['sql_prefix']}_votes WHERE `username` = '{$TMPL['username']}' AND `hash` = '" . $_COOKIE['r_'.$username] . "' AND `time` < " . $time . " LIMIT 1", __FILE__, __LINE__);
                           // echo $DB->num_rows($res);
			$row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_votes WHERE `username` = '{$TMPL['username']}' AND hash = '" . $_COOKIE['r_'.$username] . "'", __FILE__, __LINE__);
            $TMPL = array_merge($TMPL, (array)$row);				
			$u_cookie_id=$TMPL['id'];
            if($DB->num_rows($res) == 1) 
			{
                /*$DB->query("UPDATE {$CONF['sql_prefix']}_stats SET `num_ratings` = num_ratings + 1 WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
                $DB->query("UPDATE {$CONF['sql_prefix']}_stats SET `num_total_votes` = num_total_votes + 1 WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
                $DB->query("DELETE FROM {$CONF['sql_prefix']}_votes WHERE `username` = '{$TMPL['username']}' AND `ip` = '{$ip}' LIMIT 1", __FILE__, __LINE__);
                //$DB->query("INSERT INTO {$CONF['sql_prefix']}_votes_nicks VALUES ('{$TMPL['username']}', '{$TMPL['nickname']}', '{$date}', '{$ip}')", __FILE__, __LINE__);				
                setcookie('r_'.$username, '');*/

                return 1;
            } else {
                return 3;
            }
        } else {
            return 2;
        }  
    }

    function set_cookie($username, $ip) {
        global $CONF, $DB, $FORM, $LNG, $TMPL;
			$timestamp = time();
			$d=strftime('%d')+1;
            $time_st = strftime('%H:%M ('.$d.'/%m/%y)',$timestamp);        
        $hash = md5(time() + rand(1, 5000) . rand(1, 1000));
        // setcookie('r_' . $TMPL['username'], $hash, time()+60*60*24*30);
		//setcookie('wtf', $TMPL['username'], time()+99999);
        //setcookie('r_' . $TMPL['username'], $hash, time()+99999);
        $DB->query("INSERT INTO {$CONF['sql_prefix']}_votes VALUES (NULL, '{$TMPL['username']}', '{$ip}', '{$hash}', '" . time() . "', '{$time_st}')", __FILE__, __LINE__);     
    }	

  function form() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
    $TMPL = array_merge($TMPL, $row);

    $link = "<a href=\"{$TMPL['url']}\" onclick=\"out('{$TMPL['username']}');\">{$TMPL['title']}</a>";
    $TMPL['rate_message'] = sprintf($LNG['rate_message'], $link);
    
    $TMPL['rand'] = rand(1, 1000000);
    $TMPL['rate_captcha'] = $this->do_skin('join_captcha');
    
    $TMPL['content'] = $this->do_skin('rate_form');
    
  }

  function process($ip, $ip_sql) {
    global $CONF, $DB, $FORM, $TMPL, $time_st, $u_cookie_id;

    // Review
    if (isset($FORM['review']) && $FORM['review']) {
      $date = date("Y-m-d H:i:s", time() + (3600*$CONF['time_offset']));
      list($id) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_reviews", __FILE__, __LINE__);
      if (!$id) {
        $id = 1;
      }
      
      $review = strip_tags($FORM['review']);
      $review = nl2br($review);
      $review = $this->bad_words($review);

      $TMPL['review'] = $review;
      if ($CONF['email_admin_on_review']) {
        $rate_email_admin = new skin('rate_email_admin');
        $rate_email_admin->send_email($CONF['your_email']);
      }

      $review = $DB->escape($review);
      $ip == $ip_sql;
      $DB->query("INSERT INTO {$CONF['sql_prefix']}_reviews (username, id, date, review, active, ip) VALUES ('{$TMPL['username']}', {$id}, '{$date}', '{$review}', {$CONF['active_default_review']}, '{$ip}')", __FILE__, __LINE__);
    }

    $DB->query("UPDATE {$CONF['sql_prefix']}_stats SET `num_ratings` = num_ratings + 1 WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
    $DB->query("UPDATE {$CONF['sql_prefix']}_stats SET `num_total_votes` = num_total_votes + 1 WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
    if ($ip == $ip_sql) {
      $DB->query("UPDATE {$CONF['sql_prefix']}_ip_log SET rate = 1 WHERE ip_address = '$ip' AND username = '{$TMPL['username']}'", __FILE__, __LINE__);
    }
    else {
      list($id_ip_log) = $DB->fetch("SELECT MAX(id) + 1 FROM {$CONF['sql_prefix']}_ip_log", __FILE__, __LINE__);
      if (!$id_ip_log) {
        $id_ip_log = 1;
      }
	  $date = date("Y-m-d H:i:s", time() + (3600*$CONF['time_offset']));
	  $DB->query("INSERT INTO {$CONF['sql_prefix']}_ip_log (id, ip_address, username, rate, time) VALUES ('{$id_ip_log}', '{$ip}', '{$TMPL['username']}', '1', '{$date}')", __FILE__, __LINE__);
      $DB->query("DELETE FROM {$CONF['sql_prefix']}_votes WHERE `username` = '{$TMPL['username']}' AND `id` = '{$u_cookie_id}' LIMIT 1", __FILE__, __LINE__);	  
    }



    $TMPL['content'] = $this->do_skin('rate_finish');
  }
}
?>
