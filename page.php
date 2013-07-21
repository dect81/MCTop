<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class page extends base {
  function page() {
    global $CONF, $DB, $FORM, $TMPL;

    $id = $DB->escape($FORM['id']);
    list($TMPL['id'], $TMPL['header'], $TMPL['content']) = $DB->fetch("SELECT id, title, content FROM {$CONF['sql_prefix']}_custom_pages WHERE id = '{$id}'", __FILE__, __LINE__);
  }
}
?>
