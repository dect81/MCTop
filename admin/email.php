<?php

class email extends base {

  function email() {
    global $FORM, $LNG, $TMPL;
	


    $TMPL['header'] = $LNG['a_email_header'];

    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $LNG, $TMPL;

    $TMPL['admin_content'] = <<<EndHTML
<form action="" method="post" onSubmit="return confirm('Ты блять точно уверен? Мы блять разошлем дохуя мейлов!!!');">
<fieldset>
<legend>{$LNG['a_email_header']}</legend>
<label>{$LNG['a_email_subject']}<br />
<input type="text" name="subject" size="50" /><br /><br />
</label>
<label>{$LNG['a_email_message']}<br />
<textarea cols="40" rows="15" name="message"></textarea><br /><br />
</label>
<input name="submit" type="submit" value="{$LNG['a_email_header']}" />
</form>
EndHTML;
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

		//require_once ('Zend/Mail.php');
		require_once 'Zend/Loader/Autoloader.php';
    Zend_Loader_Autoloader::getInstance();
$transport = new Zend_Mail_Transport_Smtp();
 
$protocol = new Zend_Mail_Protocol_Smtp('localhost');
$protocol->connect();
$protocol->helo('mctop.su');
 
$transport->setConnection($protocol);
set_time_limit(9999999);
die();
      $message = $FORM['message'];
      $subject = $FORM['subject'];

    $failed = 0;
    $count = 0;
    $result = $DB->query("SELECT id, email, username FROM {$CONF['sql_prefix']}_servers WHERE id>201 ORDER BY id", __FILE__, __LINE__);
	try{
		while ($email = $DB->fetch_array($result)) {
			if($this->is_valid_email($email['email'])){
				$mail = new Zend_Mail('UTF-8');
				$mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
				$mail->addTo($email['email'], $email['username']);
				$mail->setFrom('noreply@mctop.su', 'MCtop');
				$mail->setSubject(
					$subject
				);
				
				$mail->setBodyHtml(str_replace("%username%", $email['username'], $message));
			 
				// Управление соединением вручную
				$protocol->rset();
				$mail->send($transport);			
				$count++;
				$last = $email['id'];
			}
		}
	} catch (Zend_Mail_Exception $e){
       $TMPL['admin_content'] .=$e;
	   $failed++;
      }catch (Zend_Mail_Transport_Exception $e){
        $TMPL['admin_content'] .= $e;
		$failed++;
      }
	$protocol->quit();
	$protocol->disconnect();

/*
    
      if(!@mail($email, $subject, $message, "From: {$TMPL['list_name']} <{$CONF['your_email']}>")) {
        $TMPL['admin_content'] .= sprintf($LNG['a_email_not_sent'], $email).".<br />\n";
        $failed++;
      }
      else {
        $TMPL['admin_content'] .= sprintf($LNG['a_email_msg_sent'], $email).".<br />\n";
        $count++;
      }
    }
*/
    $TMPL['admin_content'] .= "<br />\n".sprintf($LNG['a_email_sent'], $count);
    if ($failed) {
      $TMPL['admin_content'] .= "<br/>".$last."<br />\n".sprintf($LNG['a_email_failed'], $failed);
    } else {
		header('Location: /admin/main');
	}
  }
  function is_valid_email($email) {
  $result = TRUE;
  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
    $result = FALSE;
  }
  return $result;
}

}