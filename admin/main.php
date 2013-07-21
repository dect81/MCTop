<?php
//==============================\\

if (!defined('ATSPHP')) {
  die("This file cannot be accessed directly.");
}

class main extends base {
  function main() {
    global $DB, $CONF, $ADM, $LNG, $TMPL;
   $TMPL['admin_content'] = $this->do_skin('admin_main');
  }
}
?>