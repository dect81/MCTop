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

    $TMPL['user_cp_content'] = $this->do_skin('user_cp_support');
	}

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

$row = $DB->fetch("SELECT * FROM {$CONF['sql_prefix']}_servers WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
$TMPL = array_merge($TMPL, $row);

$EmailTo = "mctop.support1@yandex.ru";
$EmailTo2 = "mctop.support2@yandex.ru";
$subject = "Сообщение от администратора сервера";
$active = $TMPL['active'];
$reason = Trim(stripslashes($_POST['reason'])); 
$email = $TMPL['email'];
$message = Trim(stripslashes($_POST['message'])); 
$username = $TMPL['username'];
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
?>
