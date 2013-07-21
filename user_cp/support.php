<?php
if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class support extends join_edit {
  function support() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['edit_header'];
    if (!isset($FORM['submit'])) {
      $this->form();
    }
    else {
      $this->process();
    }
  }

  function form() {
    global $CONF, $DB, $LNG, $TMPL;

$row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
$TMPL = array_merge($TMPL, $row);
    if (isset($TMPL['message'])) { $TMPL['message'] = stripslashes($TMPL['message']); }
    $TMPL['user_cp_content'] = $this->do_skin('user_cp_support');
}

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;
	
	$reason = Trim(stripslashes($_POST['reason'])); 
	$message = Trim(stripslashes($_POST['message'])); 
	if(empty($reason)){
		$TMPL['error_reason'] = $LNG['support_error_reason'];
		$ec = $ec+1;
	}
	if(empty($message)){
		$ec = $ec+1;
		$TMPL['error_message'] = $LNG['support_error_message'];
	}
	if($ec > 0) {
		$this->form();
	} else {
		$row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
		$TMPL = array_merge($TMPL, $row);

		$EmailTo = "mctop.support1@yandex.ru";
		$EmailTo2 = "mctop.support2@yandex.ru";
		$subject = "Сообщение от администратора сервера";
		$active = $TMPL['active'];

		$email = $TMPL['email'];

		$username = $TMPL['username'];
		$url = $TMPL['url'];
		if($active) $active = 'Активен';
		else $active = 'Неактивен';

		// prepare email body text
		$body = "";
		$body .= "Логин: ";
		$body .= $username;
		$body .= "(";
		$body .= $active;
		$body .= ")";
		$body .= "\n";
		$body .= "URL: {$url}";
		$body .= $username;
		$body .= "\n\n";		
		$body .= "Причина: ";
		$body .= $reason;
		$body .= "\n";
		$body .= "Email: ";
		$body .= $email;
		$body .= "\n";
		$body .= "Текст: ";
		$body .= $message;
		$body .= "\n";



		// send email 
		$success = mail($EmailTo, $subject, $body, "From: <$EmailFrom>");
		$success2 = mail($EmailTo2, $subject, $body, "From: <$EmailFrom>");
		// redirect to success page 
		if ($success){
		 if ($success2){
		  $TMPL['user_cp_content'] = $this->do_skin('user_cp_support_thx');;
		  }
		}
}


      }
    }
?>
