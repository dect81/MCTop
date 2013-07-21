<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class out extends in_out {
  function out() {
    global $CONF, $DB, $FORM;

    $username = $DB->escape($FORM['u']);
    $this->record($username, 'out');

		// If $_GET['go'] is set, then forward to the member's URL
		// If it is not set, then this is being called in the background by javascript, so stop executing to conserve resources
    if (isset($_GET['go']) && $_GET['go']) {
      list($url) = $DB->fetch("SELECT url FROM {$CONF['sql_prefix']}_servers WHERE username = '{$username}'", __FILE__, __LINE__);
      header("Location: {$url}");
    }
    else {
			exit;
    }
  }
}
?>
