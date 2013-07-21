<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class lost_pw extends base {
  function lost_pw() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['lost_pw_header'];

    if (!isset($FORM['submit']) && !isset($FORM['sid'])) {
      $TMPL['content'] = $this->do_skin('lost_pw_form');
    }
    elseif (isset($FORM['submit']) && !isset($FORM['password'])) {
      $this->email();
    }
    elseif (isset($FORM['sid']) && !isset($FORM['password'])) {
      $this->form();
    }
    elseif (isset($FORM['sid']) && isset($FORM['password'])) {
      $this->new_password();
    }
  }

  function email() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $username = $DB->escape($FORM['u']);
    list($email) = $DB->fetch("SELECT email FROM {$CONF['sql_prefix']}_servers WHERE username = '{$username}'", __FILE__, __LINE__);
    if ($email) {
		$substr_count = strrpos($email,"@");
		$TMPL['email'] = substr($email, 0, $substr_count);

      require_once("{$CONF['path']}/sources/misc/session.php");
      $session = new session;
      $TMPL['sid'] = $session->create('lost_pw', $username, 0);

      $lost_pw_email = new skin('lost_pw_email');
      $lost_pw_email->send_email($email);

      $TMPL['content'] = $this->do_skin('lost_pw_finish');
    }
    else {
      $this->error($LNG['g_invalid_u']);
    }
  }

  function form() {
    global $CONF, $FORM, $LNG, $TMPL;

    require_once("{$CONF['path']}/sources/misc/session.php");
    $session = new session;
    list($type, $data) = $session->get($FORM['sid']);

    if ($type == 'lost_pw') {
      $TMPL['sid'] = strip_tags($FORM['sid']);
      $TMPL['content'] = $this->do_skin('lost_pw_form_2');
    }
    else {
      $this->error($LNG['g_session_expired']);
    }
  }

  function new_password() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    require_once("{$CONF['path']}/sources/misc/session.php");
    $session = new session;
    list($type, $data) = $session->get($FORM['sid']);
    $TMPL['username'] = $DB->escape($data);
    $password = md5($FORM['password']);

    if ($type == 'lost_pw') {
      $session->delete($FORM['sid']);

      $DB->query("UPDATE {$CONF['sql_prefix']}_servers SET password = '{$password}' WHERE username = '{$TMPL['username']}'", __FILE__, __LINE__);
      $TMPL['content'] = $this->do_skin('lost_pw_finish_2');
    }
    else {
      $this->error($LNG['g_session_expired']);
    }
  }
}
?>
